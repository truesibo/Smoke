<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 20.05.15
 * Time: 08:49
 */

namespace whm\CacheWatch\Rules;


interface Rule
{
    public function validate($response);
}