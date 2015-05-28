<?php

/**
 * Created by PhpStorm.
 * User: langn
 * Date: 22.05.15
 * Time: 11:10.
 */
class DurationRuleTest extends PHPUnit_Framework_TestCase
{
    public function testRuleSuccess()
    {
        $durationRule = new \whm\Smoke\Rules\Http\DurationRule();
        $durationRule->init(1500);

        $response = new \whm\Smoke\Http\Response('', '', 200, 1);
        $durationRule->validate($response);
    }

    /**
     * @throws \whm\Smoke\Rules\ValidationFailedException
     */
    public function testRuleFailed()
    {
        $durationRule = new \whm\Smoke\Rules\Http\DurationRule();
        $durationRule->init(1500);

        $response = new \whm\Smoke\Http\Response('', '', 200, 2);

        $this->setExpectedException('whm\Smoke\Rules\ValidationFailedException');
        $durationRule->validate($response);
    }
}
