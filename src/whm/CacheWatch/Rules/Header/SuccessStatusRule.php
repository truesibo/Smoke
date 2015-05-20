<?php

namespace whm\CacheWatch\Rules\Header;

use whm\CacheWatch\Rules\Rule;

class SuccessStatusRule implements Rule
{
    public function validate($response)
    {
        if($response["status"] >= 400) {
            return "Status code " . $response["status"] . " found.";
        }
        return true;
    }
}