<?php

namespace whm\Smoke\Http;

use Ivory\HttpAdapter\HttpAdapterInterface;
use Ivory\HttpAdapter\Message\Request;
use Ivory\HttpAdapter\MultiHttpAdapterException;

/**
 * HttpClient.
 *
 * @author Robert Schönthal <robert.schoenthal@gmail.com>
 */
class HttpClient
{
    /**
     * @var HttpAdapterInterface
     */
    private $adapter;

    public function __construct(HttpAdapterInterface $adapter)
    {
        $adapter->getConfiguration()->setMessageFactory(new MessageFactory());
        $this->adapter = $adapter;
    }

    /**
     * @param array $uris
     *
     * @return Response[]
     */
    public function request(array $uris)
    {
        $requests = [];

        foreach ($uris as $uri) {
            $requests[] = new Request((string) $uri, 'GET', 'php://memory', ['Accept-Encoding' => 'gzip'], []);
//            $requests[] = $client->createRequest('GET', $uri,
//                ['timeout' => 10,
//                 'connect_timeout' => 1.5,
//                 'headers' => ['Accept-Encoding' => 'gzip'],
//                 'verify' => false]
//            );
        }

        try {
            $responses = $this->adapter->sendRequests($requests);
        } catch (MultiHttpAdapterException $e) {
            $responses = $e->getResponses();
            $exceptions = $e->getExceptions();
        }

        return $responses;
//        $results = GuzzleHttp\Pool::batch($client, $requests);
//
//        foreach ($results as $result) {
//            if ($result instanceof GuzzleHttp\Exception\ConnectException) {
//                // @todo handle this error
//            } elseif ($result instanceof GuzzleHttp\Exception\TooManyRedirectsException) {
//                // @todo handle this error
//            } elseif ($result instanceof GuzzleHttp\Exception\RequestException) {
//                $url = $result->getRequest()->getUrl();
//                $responses[$url] = new Response($result->getResponse()->getBody()->getContents(),
//                    GuzzleHttp\Message\Response::getHeadersAsString($result->getResponse()),
//                    $result->getResponse()->getStatusCode(),
//                    null,
//                    new Request(new Uri($url)));
//            } else {
//                /* @var GuzzleHttp\Message\Response $result */
//                $url = $result->getEffectiveUrl();
//                $responses[$url] = new Response($result->getBody()->getContents(),
//                    GuzzleHttp\Message\Response::getHeadersAsString($result),
//                    $result->getStatusCode(),
//                    null,
//                    new Request(new Uri($url)));
//            }
//        }
//
//        return $responses;
    }
}
