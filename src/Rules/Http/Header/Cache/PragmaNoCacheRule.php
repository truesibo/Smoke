<?php

namespace whm\Smoke\Rules\Http\Header\Cache;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

/**
 * This rule checks if there are no "pragma: no-cache" or "cache-control: no-cache" header are set.
 */
class PragmaNoCacheRule implements Rule
{
    public function validate(Response $response)
    {
        if ($response->hasHeader('Pragma') && 'no-cache' === $response->getHeader('Pragma')[0]) {
            throw new ValidationFailedException('pragma:no-cache was found');
        }

        if ($response->hasHeader('Cache-Control') && 'no-cache' === $response->getHeader('Cache-Control')[0]) {
            throw new ValidationFailedException('cache-control:no-cache was found');
        }
    }
}
