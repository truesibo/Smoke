<?php

/*
 * This rule will find external ressources on a https transfered page that are insecure (http).
 *
 * @author Nils Langner <nils.langner@phmlabs.com>
 * @inspiredBy Christian Haller
 */

namespace whm\Smoke\Rules\Html;

use phmLabs\Base\Www\Html\Document;
use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class InsecureContentRule implements Rule
{
    public function validate(Response $response)
    {
        $request = $response->getRequest();
        if ($request->getUrl()->isSecure()) {
            $htmlDocument = new Document($response->getBody());
            $ressources = $htmlDocument->getExternalDependencies();

            foreach ($ressources as $ressource) {
                if (!$ressource->isSecure()) {
                    throw new ValidationFailedException("At least one dependency was found on a secure url, that was transfered insecure (" . $ressource->toString() . ")");
                }
            }
        }
    }
}
