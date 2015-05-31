<?php

namespace whm\Smoke\Test\Rules;

use whm\Smoke\Http\Request;
use whm\Smoke\Http\Response;
use whm\Smoke\Rules;

class RulesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider validRulesProvider
     */
    public function testValidRules(Rules\Rule $rule, array $initArgs, $body, $statusCode, array $header, array $parameters = [])
    {
        $this->runRuleTest($rule, $initArgs, $body, $statusCode, $header, $parameters);
    }

    /**
     * @dataProvider invalidRulesProvider
     * @expectedException \whm\Smoke\Rules\ValidationFailedException
     */
    public function testInValidRules(Rules\Rule $rule, array $initArgs, $body, $statusCode, array $header, array $parameters = [])
    {
        $this->runRuleTest($rule, $initArgs, $body, $statusCode, $header, $parameters);
    }

    public function validRulesProvider()
    {
        $httpsRequest = new Request('https://test.com');

        return [
            //HTTP
            //[new Rules\Http\DurationRule(), [1500], '', 200, []],
            [new Rules\Http\Header\Cache\ExpiresRule(), [], '', 200, ['Expires' => ['Thu, 19 Nov 2050 08:52:00 GMT']]],
            [new Rules\Http\Header\Cache\MaxAgeRule(), [], '', 200, ['Cache-Control' => ['max-age=200']]],
            [new Rules\Http\Header\Cache\PragmaNoCacheRule(), [], '', 200, ['Cache-Control' => ['max-age=200']]],
            [new Rules\Http\Header\GZipRule(), [],'', 200, ['Content-Encoding' => ['gzip']]],
            [new Rules\Http\Header\SuccessStatusRule(), [],'', 200, []],
            //HTML
            [new Rules\Html\ClosingHtmlTagRule(), [],'</html>', 200, ['Content-Type' => ['text/html']]],
            [new Rules\Html\CssFileCountRule(), [10],'<html><link type="text/css" href="/foo.css" /></html>', 200, ['Content-Type' => ['text/html']]],
            [new Rules\Html\JsFileCountRule(), [10],'<html><script src="/foo.js" ></script></html>', 200, ['Content-Type' => ['text/html']]],
            [new Rules\Html\InsecureContentRule(), [],'<html><script src="https://google.com/foo.js" ></script></html>', 200, ['Content-Type' => ['text/html']], ['request' => $httpsRequest]],
            [new Rules\Html\SizeRule(), [1], str_repeat('a', 1000), 200, ['Content-Type' => ['text/html']]],
            //IMAGE
            [new Rules\Image\SizeRule(), [1], str_repeat('a', 1000), 200, ['Content-Type' => ['image/jpeg']]],
            //JSON
            [new Rules\Json\ValidRule(), [], '{}', 200, ['Content-Type' => ['application/json']]],
            //XML
            //[new Rules\Xml\Rss\ValidRule(), [], '<rss></rss>', 200, ['Content-Type' => ['text/xml']]],
        ];
    }

    public function invalidRulesProvider()
    {
        $httpsRequest = new Request('https://test.com');

        return [
            //HTTP
            //[new Rules\Http\DurationRule(), [2500], '', 200, []],
            [new Rules\Http\Header\Cache\ExpiresRule(), [], '', 200, ['Expires' => ['Thu, 19 Nov 2000 08:52:00 GMT']]],
            [new Rules\Http\Header\Cache\MaxAgeRule(), [], '', 200, ['Cache-Control' => ['max-age=0']]],
            [new Rules\Http\Header\Cache\PragmaNoCacheRule(), [], '', 200, ['Cache-Control' => ['no-cache']]],
            [new Rules\Http\Header\Cache\PragmaNoCacheRule(), [], '', 200, ['Pragma' => ['no-cache']]],
            [new Rules\Http\Header\GZipRule(), [],'', 200, []],
            [new Rules\Http\Header\SuccessStatusRule(), [],'', 400, []],
            //HTML
            [new Rules\Html\ClosingHtmlTagRule(), [],'', 200, ['Content-Type' => ['text/html']]],
            [new Rules\Html\CssFileCountRule(), [1],'<html><link type="text/css" href="/foo.css" /><link type="text/css" href="/bar.css" /></html>', 200, ['Content-Type' => ['text/html']]],
            [new Rules\Html\JsFileCountRule(), [1],'<html><script src="/foo.js" ></script><script src="/bar.js" ></script></html>', 200, ['Content-Type' => ['text/html']]],
            [new Rules\Html\InsecureContentRule(), [],'<html><script src="http://google.com/foo.js" ></script></html>', 200, ['Content-Type' => ['text/html']], ['request' => $httpsRequest]],
            [new Rules\Html\SizeRule(), [1],str_repeat('a', 1001), 200, ['Content-Type' => ['text/html']]],
            //IMAGE
            [new Rules\Image\SizeRule(), [1], str_repeat('a', 1001), 200, ['Content-Type' => ['image/jpeg']]],
            //JSON
            [new Rules\Json\ValidRule(), [], 'lolcat', 200, ['Content-Type' => ['application/json']]],
            //XML
            [new Rules\Xml\Rss\ValidRule(), [], '<rss>lolcat</rss', 200, ['Content-Type' => ['text/xml']]],
        ];
    }

    /**
     * @param Rules\Rule $rule
     * @param array      $initArgs
     * @param string     $body
     * @param int        $statusCode
     * @param array      $header
     * @param array      $parameters
     */
    private function runRuleTest(Rules\Rule $rule, array $initArgs, $body, $statusCode, array $header, array $parameters)
    {
        if ($initArgs) {
            call_user_func_array([$rule, 'init'], $initArgs); // :( php5.5 splat operator would be handy
        }

        $stream = fopen('data://text/plain,' . $body, 'r');
        $response = new Response($stream, $statusCode, $header, $parameters);

        $rule->validate($response);
    }
}
