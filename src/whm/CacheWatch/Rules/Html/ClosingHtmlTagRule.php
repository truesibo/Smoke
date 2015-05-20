<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 14:23
 */

namespace whm\CacheWatch\Rules\Html;

use whm\CacheWatch\Http\Response;
use whm\CacheWatch\Rules\Rule;

class ClosingHtmlTagRule implements Rule
{
    public function validate(Response $response)
    {
        if (($response->getStatus() < 300 || $response->getStatus() >= 500) && $response->getContentType() == "text/html") {
            if( strpos($response->getBody(), "</html>") === false) {
                return "Closing html tag is missing";
            }
        }

        return true;
    }
}