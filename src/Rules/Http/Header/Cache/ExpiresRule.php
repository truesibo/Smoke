<?php

namespace whm\Smoke\Rules\Http\Header\Cache;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

/**
 * This rule checks if a expire header is in the past
 */
class ExpiresRule implements Rule
{
    public function validate(Response $response)
    {
        if (preg_match('^Expires:(.*)^', $response->getHeader(), $matches)) {
            $expireRaw = preg_replace('/[^A-Za-z0-9\-\/,]/', '', $matches[1]);
            if ($expireRaw !== "") {
                $expires = strtotime($matches[1]);
                if ($expires < time()) {
                    throw new ValidationFailedException('expires in the past');
                }
            }
        }
    }
}
