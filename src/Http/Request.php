<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 24.05.15
 * Time: 20:18
 */

namespace whm\Smoke\Http;

use phmLabs\Base\Www\Uri;

class Request
{
    private $url;

    public function __construct(Uri $url)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
}
