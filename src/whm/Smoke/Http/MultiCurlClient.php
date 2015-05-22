<?php

namespace whm\Smoke\Http;

class MultiCurlClient
{
    public static function request(array $uris)
    {
        $data = [];

        foreach ($uris as $uri) {
            $data[$uri->toString()] = $uri->toString();
        }

        $curly  = [];
        $result = [];

        $mh = curl_multi_init();

        foreach ($data as $id => $d) {
            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 1);
            curl_setopt($curly[$id], CURLOPT_VERBOSE, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($curly[$id], CURLOPT_ENCODING, '');

            curl_multi_add_handle($mh, $curly[$id]);
        }

        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        foreach ($curly as $id => $c) {
            $response = curl_multi_getcontent($c);

            $statuscode  = curl_getinfo($c, CURLINFO_HTTP_CODE);
            $duration    = curl_getinfo($c, CURLINFO_STARTTRANSFER_TIME);
            $header_size = curl_getinfo($c, CURLINFO_HEADER_SIZE);
            $header      = substr($response, 0, $header_size);
            $body        = substr($response, $header_size);

            $result[$id] = new Response($body, $header, $statuscode, $duration);

            curl_multi_remove_handle($mh, $c);
        }

        curl_multi_close($mh);

        return $result;
    }
}
