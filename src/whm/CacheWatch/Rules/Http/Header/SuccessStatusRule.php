<?php

namespace whm\CacheWatch\Rules\Http\Header;

use whm\CacheWatch\Http\Response;
use whm\CacheWatch\Rules\Rule;

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