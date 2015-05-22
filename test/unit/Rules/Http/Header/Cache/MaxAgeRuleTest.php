<?php

class MaxAgeRuleTest extends PHPUnit_Framework_TestCase
{
    public function testRuleSuccess()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\MaxAgeRule();

        $response = new \whm\Smoke\Http\Response('', 'cache-control:max-age=200', 200, 1);
        $rule->validate($response);
    }

    /**
     * @throws \whm\Smoke\Rules\ValidationFailedException
     */
    public function testRuleFailed()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\Cache\MaxAgeRule();

        $response = new \whm\Smoke\Http\Response('', 'cache-control:max-age=0', 200, 1);

        $this->setExpectedException('whm\Smoke\Rules\ValidationFailedException');
        $rule->validate($response);
    }
}
