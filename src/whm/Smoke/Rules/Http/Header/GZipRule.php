<?php

namespace whm\Smoke\Rules\Http\Header;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;

class GZipRule implements Rule
{
    public function validate(Response $response)
    {
        if (strpos($response->getContentType(), "image") !== false) {
            return true;
        }

        if (strpos($response->getHeader(true), "content-encoding:gzip") === false) {
            return "gzip compression not active";
        }
        return true;
    }
}