<?php

namespace whm\CacheWatch\Rules\Header\Cache;

use whm\CacheWatch\Http\Response;
use whm\CacheWatch\Rules\Rule;

class ExpiresRule implements Rule
{
    public function  validate(Response $response) {

        if (preg_match("^Expires: (.*)^", $response->getHeader(), $matches)) {
            $expires = strtotime($matches[1]);
            if ($expires < time()) {
                return "expires in the past";
            }
        }
        return true;
    }
}