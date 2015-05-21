<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 14:23
 */

namespace whm\Smoke\Rules\Image;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class SizeRule implements Rule
{
    private $maxSize;

    public function init($maxSize = 100)
    {
        $this->maxSize = $maxSize;
    }

    public function validate(Response $response)
    {
        if (strpos($response->getContentType(), 'image') !== false) {
            $size = strlen($response->getBody()) / 1000;
            if ($size > $this->maxSize) {
                throw new ValidationFailedException("the size of the file is too big (" . $size . " KB)");
            }
        }
    }
}