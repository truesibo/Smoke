<?php

namespace whm\Smoke\Scanner;

use phmLabs\Base\Www\Html\Document;
use phmLabs\Base\Www\Uri;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use whm\Smoke\Config\Configuration;
use whm\Smoke\Http\MultiCurlClient;
use whm\Smoke\Rules\ValidationFailedException;

class Scanner
{
    const ERROR = 'ERROR';
    const PASSED = 'PASSED';

    private $numParallelRequests;
    private $output;

    private $whitelist;
    private $blacklist;

    private $rules = [];

    private $configuration;

    public function __construct(Uri $uri, OutputInterface $output, Configuration $config, $numUrl = 100, $parallelRequests = 1)
    {
        $this->numParallelRequests = $parallelRequests;

        $this->pageContainer = new PageContainer($numUrl);
        $this->pageContainer->push($uri, $uri);

        $this->output = $output;

        $this->blacklist = $config->getBlacklist();
        $this->whitelist = $config->getWhitelist();

        $this->rules = $config->getRules();

        $this->configuration = $config;
    }

    public function scan()
    {
        $violations = [];

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
                        if ($this->configuration->isUriAllowed($uriToAdd)) {
                            $this->pageContainer->push($uriToAdd, $currentUri);
                        }
                    }
                }

                $messages = $this->checkResponse($response);
                if (count($messages) > 0) {
                    $violations[$currentUri->toString()]['messages'] = $messages;
                    $violations[$currentUri->toString()]['type'] = self::ERROR;
                } else {
                    $violations[$currentUri->toString()]['type'] = self::PASSED;
                }
                $violations[$currentUri->toString()]['parent'] = $this->pageContainer->getParent($currentUri);

                $progress->advance();
            }

            $urls = $this->pageContainer->pop($this->numParallelRequests);
        }

        $progress->finish();

        return $violations;
    }

    private function checkResponse($response)
    {
        $messages = [];

        foreach ($this->rules as $rule) {
            try {
                $result = $rule->validate($response);
            } catch (ValidationFailedException $e) {
                $messages[] = $e->getMessage();
            }
        }

        return $messages;
    }
}