<?php

namespace whm\Smoke\Rules\Html;

use phmLabs\Base\Www\Html\Document;
use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class JsFileCountRule implements Rule
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
            $jsFiles = $document->getExternalDependencies(array("js"));

            if (count($jsFiles) > $this->maxCount) {
                throw new ValidationFailedException("Too many (" . count($jsFiles) . ") js files were found.");
            }
        }
    }
}
