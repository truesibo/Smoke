<?php

namespace whm\Smoke\Rules\Http;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class DurationRule implements Rule
{
    private $maxDuration;

    public function init($maxDuration = 1000)
    {
        $this->maxDuration = $maxDuration;
    }

    public function validate(Response $response)
    {
        if ($response->getDuration() * 1000 > $this->maxDuration) {
            throw new ValidationFailedException('the http request lasted ' . $response->getDuration() . ' seconds.');
        }
    }
}
