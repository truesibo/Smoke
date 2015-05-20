<?php

namespace whm\CacheWatch\Scanner;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use whm\CacheWatch\Http\MultiCurlClient;

use phmLabs\Base\Www\Html\Document;
use phmLabs\Base\Www\Uri;
use whm\CacheWatch\Rules\Header\Cache\ExpiresRule;
use whm\CacheWatch\Rules\Header\Cache\MaxAgeRule;
use whm\CacheWatch\Rules\Header\Cache\PragmaNoCacheRule;
use whm\CacheWatch\Rules\Header\SuccessStatusRule;
use whm\CacheWatch\Rules\Html\ClosingHtmlTagRule;

class Scanner
{
    const ERROR = "ERROR";
    const PASSED = "PASSED";

    private $numParallelRequests;
    private $output;

    private $whitelist;
    private $blacklist;

    private $rules = array();

    public function __construct(Uri $uri, OutputInterface $output, array $whitelist, array $blacklist, $numUrl = 100, $parallelRequests = 1)
    {
        $this->numParallelRequests = $parallelRequests;

        $this->pageContainer = new PageContainer($numUrl);
        $this->pageContainer->push($uri);

        $this->output = $output;

        $this->blacklist = $blacklist;
        $this->whitelist = $whitelist;

        $this->rules[] = new MaxAgeRule();
        $this->rules[] = new PragmaNoCacheRule();
        $this->rules[] = new ExpiresRule();
        $this->rules[] = new SuccessStatusRule();
        $this->rules[] = new ClosingHtmlTagRule();
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
        $progress->setBarWidth(100);
        $progress->start();

        while (count($urls) > 0) {

            $responses = MultiCurlClient::request($urls);

            foreach ($responses as $url => $response) {
                $currentUri = new Uri($url);

                $htmlDocument = new Document($response->getBody());
                $referencedUris = $htmlDocument->getReferencedUris();

                foreach ($referencedUris as $uri) {
                    $uriToAdd = $currentUri->concatUri($uri->toString());

                    if (true || Uri::isValid($uriToAdd->toString())) {
                        if ($this->isUriAllowed($uriToAdd)) {
                            $this->pageContainer->push($uriToAdd);
                        }
                    }
                }

                $messages = $this->checkResponse($response);
                if (count($messages) > 0) {
                    $violations[$currentUri->toString()]["messages"] = $messages;
                    $violations[$currentUri->toString()]["type"] = self::ERROR;
                } else {
                    $violations[$currentUri->toString()]["type"] = self::PASSED;
                }

                $progress->advance();
            }

            $urls = $this->pageContainer->pop($this->numParallelRequests);
        }

        $progress->finish();

        return $violations;
    }

    private function checkResponse($response)
    {
        $messages = array();

        foreach ($this->rules as $rule) {
            $result = $rule->validate($response);
            if ($result !== true) {
                $messages[] = $result;
            }
        }

        return $messages;
    }
}