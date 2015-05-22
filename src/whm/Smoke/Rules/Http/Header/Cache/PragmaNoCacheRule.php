<?php

namespace whm\Smoke\Rules\Http\Header\Cache;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class PragmaNoCacheRule implements Rule
{
    public function validate(Response $response)
    {
        $header = $response->getHeader(true);

        if (strpos($header, 'pragma:no-cache') !== false) {
            throw new ValidationFailedException('pragma:no-cache was found');
        }

        if (strpos($header, 'cache-control:no-cache') !== false) {
            throw new ValidationFailedException('cache-control:no-cache was found');
        }
    }
}
