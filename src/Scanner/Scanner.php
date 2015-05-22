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

    private $progressBar;
    private $configuration;

    public function __construct(Configuration $config, ProgressBar $progressBar)
    {
        $this->pageContainer = new PageContainer($config->getContainerSize());
        $this->pageContainer->push($config->getStartUri(), $config->getStartUri());
        $this->progressBar = $progressBar;
        $this->configuration = $config;
    }

    private function processHtmlContent($htmlContent, Uri $currentUri)
    {
        $htmlDocument = new Document($htmlContent);
        $referencedUris = $htmlDocument->getReferencedUris();

        foreach ($referencedUris as $uri) {
            $uriToAdd = $currentUri->concatUri($uri->toString());

            if (true || Uri::isValid($uriToAdd->toString())) {
                if ($this->configuration->isUriAllowed($uriToAdd)) {
                    $this->pageContainer->push($uriToAdd, $currentUri);
                }
            }
        }
    }

    public function scan()
    {
        $violations = [];
        $urls = $this->pageContainer->pop();

        while (count($urls) > 0) {
            $responses = MultiCurlClient::request($urls);

            foreach ($responses as $url => $response) {

                $currentUri = new Uri($url);

                $this->processHtmlContent($response->getBody(), $currentUri);

                $messages = $this->checkResponse($response);
                if ($messages) {
                    $violations[$url] = array('messages' => $messages, 'type' => self::ERROR);
                } else {
                    $violations[$url] = array('type' => self::PASSED);
                }
                $violations[$url]['parent'] = $this->pageContainer->getParent($currentUri);

                $this->progressBar->advance();
            }

            $urls = $this->pageContainer->pop($this->configuration->getParallelRequestCount());
        }

        return $violations;
    }

    private function checkResponse($response)
    {
        $messages = [];

        foreach ($this->configuration->getRules() as $rule) {
            try {
                $rule->validate($response);
            } catch (ValidationFailedException $e) {
                $messages[] = $e->getMessage();
            }
        }

        return $messages;
    }
}