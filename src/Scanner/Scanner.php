<?php

namespace whm\Smoke\Scanner;

use phmLabs\Base\Www\Html\Document;
use phmLabs\Base\Www\Uri;
use Symfony\Component\Console\Helper\ProgressBar;
use whm\Smoke\Config\Configuration;
use whm\Smoke\Console\NullProgressBar;
use whm\Smoke\Http\HttpClient;
use whm\Smoke\Http\Response;
use whm\Smoke\Rules\ValidationFailedException;

class Scanner
{
    const ERROR = 'error';
    const PASSED = 'passed';

    private $progressBar;
    private $configuration;
    /**
     * @var HttpClient
     */
    private $client;

    public function __construct(Configuration $config, HttpClient $client, ProgressBar $progressBar = null)
    {
        $this->pageContainer = new PageContainer($config->getContainerSize());
        $this->pageContainer->push($config->getStartUri(), $config->getStartUri());
        $this->client = $client;
        $this->configuration = $config;
        $this->progressBar = $progressBar ?: new NullProgressBar();
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
            $responses = $this->client->request($urls);

            foreach ($responses as $response) {
                $currentUri = new Uri((string) $response->getParameters()['request']->getUri());

                // only extract urls if the content type is text/html
                if ('text/html' === $response->getContentType()) {
                    $this->processHtmlContent($response->getBody(), $currentUri);
                }

                $violation = $this->checkResponse($response);
                $violation['parent'] = $this->pageContainer->getParent($currentUri);
                $violation['contentType'] = $response->getHeader('Content-Type')[0];
                $violations[$response->getUri()] = $violation;

                $this->progressBar->advance();
            }
        } while (count($urls) > 0);

        return $violations;
    }

    private function checkResponse(Response $response)
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
