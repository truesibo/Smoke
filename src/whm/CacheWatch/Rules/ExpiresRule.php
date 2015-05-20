<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:48
 */

namespace whm\CacheWatch\Rules;


class ExpiresRule implements Rule
{
    public function validate($response) {

        if (preg_match("^Expires: (.*)^", $response["header"], $matches)) {
            $expires = strtotime($matches[1]);
            if ($expires < time()) {
                return "expires in the past";
            }
        }
        return true;
    }
}