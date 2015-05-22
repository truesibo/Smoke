<?php

namespace whm\Smoke\Rules\Http\Header;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class SuccessStatusRule implements Rule
{
    public function validate(Response $response)
    {
        if ($response->getStatus() >= 400) {
            throw new ValidationFailedException('Status code ' . $response->getStatus() . ' found.');
        }
    }
}
