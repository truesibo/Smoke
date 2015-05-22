<?php

class ExpiresRuleTest extends PHPUnit_Framework_TestCase
{
    public function testRuleSuccess()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\ExpiresRule();

        $response = new \whm\Smoke\Http\Response('', 'Expires:Thu, 19 Nov 2050 08:52:00 GMT', 200, 1);
        $rule->validate($response);
    }

    /**
     * @throws \whm\Smoke\Rules\ValidationFailedException
     */
    public function testRuleFailed()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\ExpiresRule();

        $response = new \whm\Smoke\Http\Response('', 'Expires:Thu, 19 Nov 2000 08:52:00 GMT', 200, 1);

        $this->setExpectedException('whm\Smoke\Rules\ValidationFailedException');
        $rule->validate($response);
    }
}
