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

class SizeRule implements Rule
{
    private $maxSize;

    /**
     * @param $maxSizeInKB
     */
    public function __construct($maxSizeInKB = 500) {
        $this->maxSize = $maxSizeInKB;
    }

    public function validate(Response $response)
    {
        $size = strlen($response->getBody()) / 1000;
        if ( $size > $this->maxSize ) {
            return "The size of the file is to big (" . $size . " KB)";
        }

        return true;
    }
}