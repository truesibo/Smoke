<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:48
 */

namespace whm\Smoke\Rules\Http\Header\Cache;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;

class PragmaNoCacheRule implements Rule
{
    public function validate(Response $response)
    {
        $header = $response->getHeader(true);

        if (strpos($header, "pragma:no-cache") !== false) {
            return "pragma:no-cache was found";
        }

        if (strpos($header, "cache-control:no-cache") !== false) {
            return "cache-control:no-cache was found";
        }

        return true;
    }
}