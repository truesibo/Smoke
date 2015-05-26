<?php

namespace whm\Smoke\Rules\Html;

use phmLabs\Base\Www\Html\Document;
use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class CssFileCountRule implements Rule
{
    private $maxCount;

    public function init($maxCount = 10)
    {
        $this->maxCount = $maxCount;
    }

    /**
     * @param Response $response
     */
    public function validate(Response $response)
    {
        if ($response->getContentType() === 'text/html') {

            $document = new Document($response->getBody());
            $cssFiles = $document->getExternalDependencies(array("css"));

            if (count($cssFiles) > $this->maxCount) {
                throw new ValidationFailedException("Too many (" . count($cssFiles) . ") css files were found.");
            }
        }
    }
}
