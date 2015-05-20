<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 14:58
 */

namespace whm\CacheWatch\Rules\Http;


use whm\CacheWatch\Http\Response;
use whm\CacheWatch\Rules\Rule;

class DurationRule implements Rule
{
    private $maxDuration;

    public function __construct($maxDurationInMilliseconds = 1000)
    {
        $this->maxDuration = $maxDurationInMilliseconds;
    }

    public function validate(Response $response)
    {
        if( $response->getDuration() * 1000 > $this->maxDuration ) {
            return "the http request lasted " . $response->getDuration() . " seconds.";
        }

        return true;
    }
}