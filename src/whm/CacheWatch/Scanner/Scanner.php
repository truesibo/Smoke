<?php

namespace whm\CacheWatch\Scanner;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use whm\CacheWatch\Http\MultiCurlClient;

use phmLabs\Base\Www\Html\Document;
use phmLabs\Base\Www\Uri;

class Scanner
{
    const ERROR = "ERROR";
    const PASSED = "PASSED";

    private $numParallelRequests;
    private $output;

    private $whitelist;
    private $blacklist;

    public function __construct(Uri $uri, OutputInterface $output, array $whitelist, array $blacklist, $numUrl = 100, $parallelRequests = 1)
    {
        $this->numParallelRequests = $parallelRequests;

        $this->pageContainer = new PageContainer($numUrl);
        $this->pageContainer->push($uri);

        $this->output = $output;

        $this->blacklist = $blacklist;
        $this->whitelist = $whitelist;
    }

    private function isUriAllowed(Uri $uri)
    {
        foreach ($this->whitelist as $whitelist) {
            if (preg_match($whitelist, $uri->toString())) {
                foreach ($this->blacklist as $blacklist) {
                    if (preg_match($blacklist, $uri->toString())) {
                        return false;
                    }
                }
                return true;
            }
        }
        return false;
    }

    public function scan()
    {
        $violations = array();

        $urls = $this->pageContainer->pop($this->numParallelRequests);

        $progress = new ProgressBar($this->output, $this->pageContainer->getMaxSize());
        $progress->start();

        while (count($urls) > 0) {

            $responses = MultiCurlClient::request($urls);

            foreach ($responses as $url => $response) {
                $currentUri = new Uri($url);

                $htmlDocument = new Document($response["content"]);
                $referencedUris = $htmlDocument->getReferencedUris();

                foreach ($referencedUris as $uri) {
                    $uriToAdd = $currentUri->concatUri($uri->toString());
                    if (Uri::isValid($uriToAdd->toString())) {
                        if ($this->isUriAllowed($uriToAdd)) {
                            $this->pageContainer->push($uriToAdd);
                        }
                    }
                }

                $result = $this->checkHeader($response["header"]);
                if ($result !== false) {
                    $violations[$currentUri->toString()]["message"] = $result;
                    $violations[$currentUri->toString()]["type"] = self::ERROR;
                } else {
                    $violations[$currentUri->toString()]["message"] = "all tests passed";
                    $violations[$currentUri->toString()]["type"] = self::PASSED;
                }

                $progress->advance();
            }

            $urls = $this->pageContainer->pop($this->numParallelRequests);
        }

        $progress->finish();

        return $violations;
    }

    private function checkHeader($header)
    {
        // @todo create rule classes
        // @todo same rules as in livetest2?

        $normalizedHeader = strtolower($header);
        $normalizedHeader = str_replace(" ", "", $normalizedHeader);

        $indicators = array("max-age=0", 'pragma:no-cache', 'cache-control:no-cache');

        foreach ($indicators as $indicator) {
            if (strpos($normalizedHeader, $indicator) !== false) {
                return '"' . $indicator . "\" was found";
            }
        }

        $mustHaves = array('max-age');

        foreach ($mustHaves as $mustHave) {
            if (strpos($normalizedHeader, $mustHave) === false) {
                return '"' . $mustHave . "\" was not found";
            }
        }

        if (preg_match("^Expires: (.*)^", $header, $matches)) {
            $expires = strtotime($matches[1]);
            if ($expires < time()) {
                return "expires in past";
            }
        }

        // no expires date and no max-age

        return false;
    }
}