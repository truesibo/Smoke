<?php

namespace whm\Smoke\Rules\Html;

use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class SizeRule implements Rule
{
    private $maxSize;

    /**
     * @param $maxSizeInKB
     */
    public function __construct($maxSizeInKB = 100)
    {
        $this->maxSize = $maxSizeInKB;
    }

    public function init($maxSize = 100)
    {
        $this->maxSize = $maxSize;
    }

    public function validate(Response $response)
    {
        if ($response->getContentType() === 'text/html') {
            $size = strlen($response->getBody()) / 1000;
            if ($size > $this->maxSize) {
                throw new ValidationFailedException('The size of the file is too big (' . $size . ' KB)');
            }
        }
    }
}
