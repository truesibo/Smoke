<?php

namespace whm\CacheWatch\Rules\Header\Cache;

use whm\CacheWatch\Rules\Rule;

class ExpiresRule implements Rule
{
    public function validate($response) {

        if (preg_match("^Expires: (.*)^", $response["header"], $matches)) {
            $expires = strtotime($matches[1]);
            if ($expires < time()) {
                return "expires in the past";
            }
        }
        return true;
    }
}