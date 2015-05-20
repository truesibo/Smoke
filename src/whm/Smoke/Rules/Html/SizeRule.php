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