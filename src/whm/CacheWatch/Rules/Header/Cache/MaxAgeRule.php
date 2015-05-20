<?php

namespace whm\CacheWatch\Rules\Header\Cache;

use whm\CacheWatch\Http\Response;
use whm\CacheWatch\Rules\Rule;

class MaxAgeRule implements Rule
{
    public function validate(Response $response)
    {
        if (strpos($response->getHeader(true), "max-age=0") !== false) {
            return "max-age=0 was found";
        }
        return true;
    }
}