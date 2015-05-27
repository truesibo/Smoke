<?php

namespace whm\Smoke\Rules\Http\Header;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

/**
 * This rule checks if gzip compressions is activated.
 */
class GZipRule implements Rule
{
    public function validate(Response $response)
    {
        if (strpos($response->getContentType(), 'image') === false) {
            if (strpos($response->getHeader(true), 'content-encoding:gzip') === false) {
                throw new ValidationFailedException('gzip compression not active');
            }
        }
    }
}
