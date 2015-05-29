<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 29.05.15
 * Time: 21:20
 */

namespace whm\Smoke\Rules\Js;


use whm\Smoke\Http\Response;
use whm\Smoke\Rules\Rule;
use whm\Smoke\Rules\ValidationFailedException;

class SyntaxRule implements Rule
{
    private $jsHintExecutable;
    private $tmpDir;

    /**
     * @param $jsHintExecutable
     */
    public function init($jsHintExecutable = "", $tmpDir = "/tmp")
    {
        $this->jsHintExecutable = $jsHintExecutable;
        $this->tmpDir = $tmpDir;
    }

    public function validate(Response $response)
    {
        if ($response->getContentType() === "application/javascript") {
            $filename = $this->tmpDir . DIRECTORY_SEPARATOR . md5($response->getBody()) . ".js";
            file_put_contents($filename, $response->getBody());
            $conf = __DIR__ . DIRECTORY_SEPARATOR . "jsHint.conf";

            $command = $this->jsHintExecutable . " --config " . $conf . " --verbose " . $filename . " | grep -E E[0-9]+.$";
            $validationResult = shell_exec($command);

            if( !is_null($validationResult)) {
                $errorMsg = str_replace($filename . ":", "", $validationResult);
                throw new ValidationFailedException("JavaScript error found: " . $errorMsg);
            }
        }
    }

}