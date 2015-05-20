<?php

namespace whm\Smoke\Rules\Http\Header\Cache;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;

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