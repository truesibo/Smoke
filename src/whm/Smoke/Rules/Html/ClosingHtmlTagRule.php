<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 14:23
 */

namespace whm\Smoke\Rules\Html;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;

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