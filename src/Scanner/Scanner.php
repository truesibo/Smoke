<?php

namespace whm\Smoke\Scanner;

use phmLabs\Base\Www\Html\Document;
use phmLabs\Base\Www\Uri;
use Symfony\Component\Console\Helper\ProgressBar;
use whm\Smoke\Config\Configuration;
use whm\Smoke\Console\NullProgressBar;
use whm\Smoke\Http\MultiCurlClient;
use whm\Smoke\Rules\ValidationFailedException;

class Scanner
{
    const ERROR = 'error';
    const PASSED = 'passed';

    private $progressBar;
    private $configuration;

    public function __construct(Configuration $config, ProgressBar $progressBar = null)
    {
        $this->pageContainer = new PageContainer($config->getContainerSize());
        $this->pageContainer->push($config->getStartUri(), $config->getStartUri());

        $this->configuration = $config;

        if (is_null($progressBar)) {
            $this->progressBar = new NullProgressBar();
        } else {
            $this->progressBar = $progressBar;
        }
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

        do {
            $urls = $this->pageContainer->pop($this->configuration->getParallelRequestCount());
            $responses = MultiCurlClient::request($urls);

            foreach ($responses as $url => $response) {
                $currentUri = new Uri($url);

                // only extract urls if the content type is text/html
                if ($response->getContentType() === "text/html") {
                    $this->processHtmlContent($response->getBody(), $currentUri);
                }

                $violation = $this->checkResponse($response);
                $violation['parent'] = $this->pageContainer->getParent($currentUri);
                $violations[$url] = $violation;

                $this->progressBar->advance();
            }
        } while (count($urls) > 0);

        return $violations;
    }

    private function checkResponse($response)
    {
        $messages = [];

        foreach ($this->configuration->getRules() as $name => $rule) {
            try {
                $rule->validate($response);
            } catch (ValidationFailedException $e) {
                $messages[$name] = $e->getMessage();
            }
        }

        if ($messages) {
            $violation = array('messages' => $messages, 'type' => self::ERROR);
        } else {
            $violation = array('messages' => array(), 'type' => self::PASSED);
        }

        return $violation;
    }
}
