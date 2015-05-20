<?php

namespace whm\Smoke\Rules\Http\Header;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;

class SuccessStatusRule implements Rule
{
    public function validate(Response $response)
    {
        if($response->getStatus() >= 400) {
            return "Status code " . $response->getStatus() . " found.";
        }
        return true;
    }
}