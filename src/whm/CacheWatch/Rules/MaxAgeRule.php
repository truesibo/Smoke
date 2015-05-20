<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:48
 */

namespace whm\CacheWatch\Rules;


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