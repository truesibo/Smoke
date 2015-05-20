<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 19.05.15
 * Time: 13:02
 */

namespace whm\CacheWatch\Http;


class MultiCurlClient
{
    public static function request(array $uris)
    {
        $data = array();

        foreach ($uris as $uri) {
            $data[$uri->toString()] = $uri->toString();
        }

        // array of curl handles
        $curly = array();
        // data to be returned
        $result = array();

        // multi handle
        $mh = curl_multi_init();

        // loop through $data and create curl handles
        // then add them to the multi-handle
        foreach ($data as $id => $d) {

            $curly[$id] = curl_init();

            $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
            curl_setopt($curly[$id], CURLOPT_URL, $url);
            curl_setopt($curly[$id], CURLOPT_HEADER, 1);
            curl_setopt($curly[$id], CURLOPT_VERBOSE, 0);
            curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);

            curl_multi_add_handle($mh, $curly[$id]);
        }

        // execute the handles
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);


        // get content and remove handles
        foreach ($curly as $id => $c) {
            $response = curl_multi_getcontent($c);

            $statuscode = curl_getinfo($c, CURLINFO_HTTP_CODE);
            $header_size = curl_getinfo($c, CURLINFO_HEADER_SIZE);
            $header = substr($response, 0, $header_size);
            $body = substr($response, $header_size);

            $result[$id]["content"] = $body;
            $result[$id]["header"] = $header;
            $result[$id]["status"] = $statuscode;

            curl_multi_remove_handle($mh, $c);
        }

        // all done
        curl_multi_close($mh);

        return $result;
    }
}