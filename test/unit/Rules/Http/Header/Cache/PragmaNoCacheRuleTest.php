<?php

class PragmaNoCacheRuleTest extends PHPUnit_Framework_TestCase
{
    public function testRuleSuccess()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\PragmaNoCacheRule();

        $response = new \whm\Smoke\Http\Response("", "cache-control:max-age=200", 200, 1);
        $rule->validate($response);
    }

    /**
     * @throws \whm\Smoke\Rules\ValidationFailedException
     */
    public function testRuleFailedPragma()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\PragmaNoCacheRule();

        $response = new \whm\Smoke\Http\Response("", "pragma:no-cache", 200, 1);

        $this->setExpectedException('whm\Smoke\Rules\ValidationFailedException');
        $rule->validate($response);
    }

    /**
     * @throws \whm\Smoke\Rules\ValidationFailedException
     */
    public function testRuleFailedCacheControl()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\PragmaNoCacheRule();

        $response = new \whm\Smoke\Http\Response("", "cache-control:no-cache", 200, 1);

        $this->setExpectedException('whm\Smoke\Rules\ValidationFailedException');
        $rule->validate($response);
    }
}
