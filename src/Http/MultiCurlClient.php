<?php

namespace whm\Smoke\Http;

use GuzzleHttp;

class MultiCurlClient
{
    public static function request(array $uris)
    {
        $client = new GuzzleHttp\Client();

        $responses = [];
        $requests = [];

        foreach ($uris as $uri) {
            $requests[] = $client->createRequest('GET', $uri);
        }

        $results = GuzzleHttp\Pool::batch($client, $requests);

        foreach ($results as $result) {
            if ($result instanceof GuzzleHttp\Exception\ConnectException) {
                $responses[$result->getRequest()->getUrl()] = new Response($result->getResponse()->getBody()->getContents(), GuzzleHttp\Message\Response::getHeadersAsString($result->getResponse()), $result->getResponse()->getStatusCode());

            } else {
                /** @var GuzzleHttp\Message\Response $result */
                $responses[$result->getEffectiveUrl()] = new Response($result->getBody()->getContents(), GuzzleHttp\Message\Response::getHeadersAsString($result), $result->getStatusCode());
            }
        }

        return $responses;
    }
}
