<?php

namespace whm\Smoke\Rules\Http\Header\Cache;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class MaxAgeRule implements Rule
{
    public function validate(Response $response)
    {
        if (strpos($response->getHeader(true), "max-age=0") !== false) {
            throw new ValidationFailedException("max-age=0 was found");
        }
    }
}