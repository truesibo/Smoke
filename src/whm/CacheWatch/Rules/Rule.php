<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:49
 */

namespace whm\CacheWatch\Rules;

use whm\CacheWatch\Http\Response;

interface Rule
{
    public function validate(Response $response);
}