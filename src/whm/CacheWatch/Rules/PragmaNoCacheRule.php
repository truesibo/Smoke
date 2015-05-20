<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:48
 */

namespace whm\CacheWatch\Rules;


class PragmaNoCacheRule implements Rule
{
    public function validate($response)
    {
        $response["header"] = strtolower(str_replace(" ", "", $response["header"]));

        if (strpos($response["header"], "pragma:no-cache") !== false) {
            return "pragma:no-cache was found";
        }

        if (strpos($response["header"], "cache-control:no-cache") !== false) {
            return "cache-control:no-cache was found";
        }

        return true;
    }
}