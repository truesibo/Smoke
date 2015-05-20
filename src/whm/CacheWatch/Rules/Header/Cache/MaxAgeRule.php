<?php

namespace whm\CacheWatch\Rules\Header\Cache;

use whm\CacheWatch\Rules\Rule;

class MaxAgeRule implements Rule
{
    public function validate($response)
    {
        $response["header"] = strtolower(str_replace(" ", "", $response["header"]));

        if (strpos($response["header"], "max-age=0") !== false) {
            return "max-age=0 was found";
        }
        return true;
    }
}