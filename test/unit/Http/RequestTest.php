<?php

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testRequest()
    {
        $uri = new \phmLabs\Base\Www\Uri('http://smoke.phmlabs.com');
        $request = new \whm\Smoke\Http\Request($uri);

        $this->assertEquals($uri, $request->getUrl());
    }
}
