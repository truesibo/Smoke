<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:49
 */

namespace whm\Smoke\Rules;

use whm\Smoke\Http\Response;

interface Rule
{
    public function validate(Response $response);
}