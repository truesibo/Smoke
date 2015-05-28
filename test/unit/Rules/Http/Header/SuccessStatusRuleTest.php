<?php

class SuccessStatusRuleTest extends PHPUnit_Framework_TestCase
{
    public function testRuleSuccess()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\SuccessStatusRule();

        $response = new \whm\Smoke\Http\Response('', 'Content-Encoding: gzip', 200, 1);
        $rule->validate($response);
    }

    /**
     * @throws \whm\Smoke\Rules\ValidationFailedException
     */
    public function testRuleFailed()
    {
        $rule = new \whm\Smoke\Rules\Http\Header\SuccessStatusRule();

        $response = new \whm\Smoke\Http\Response('', 'Some other header', 400, 1);

        $this->setExpectedException('whm\Smoke\Rules\ValidationFailedException');
        $rule->validate($response);
    }
}
