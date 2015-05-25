<?php

namespace whm\Smoke\Http;

use GuzzleHttp;
use phmLabs\Base\Www\Uri;

class MultiCurlClient
{
    public static function request(array $uris)
    {
        $client = new GuzzleHttp\Client();

        $responses = [];
        $requests = [];

        foreach ($uris as $uri) {
            $requests[] = $client->createRequest('GET', $uri, ['headers' => ['Accept-Encoding' => 'gzip'], 'verify' => false]);
        }

        $results = GuzzleHttp\Pool::batch($client, $requests);

        foreach ($results as $result) {
            if ($result instanceof GuzzleHttp\Exception\RequestException) {
                $url = $result->getRequest()->getUrl();
                $responses[$url] = new Response($result->getResponse()->getBody()->getContents(),
                    GuzzleHttp\Message\Response::getHeadersAsString($result->getResponse()),
                    $result->getResponse()->getStatusCode(),
                    null,
                    new Request(new Uri($url)));
            } else {
                /* @var GuzzleHttp\Message\Response $result */
                $url = $result->getEffectiveUrl();
                $responses[$url] = new Response($result->getBody()->getContents(),
                    GuzzleHttp\Message\Response::getHeadersAsString($result),
                    $result->getStatusCode(),
                    null,
                    new Request(new Uri($url)));
            }
        }

        return $responses;
    }
}
