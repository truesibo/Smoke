<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $testBody = 'TestBodyWith<strong>special</strong>Chäräctörs';
        $testHeader = 'Test Header ';
        $testStatus = 750;
        $testDuration = 300;
        $testUri = new \phmLabs\Base\Www\Uri('http://smoke.phmlabs.com');
        $testRequest = new \whm\Smoke\Http\Request($testUri);

        $response = new \whm\Smoke\Http\Response($testBody, $testHeader, $testStatus, $testDuration, $testRequest);

        $this->assertEquals($testBody, $response->getBody());
        $this->assertEquals($testHeader, $response->getHeader());
        $this->assertEquals("testheader", $response->getHeader(true));
        $this->assertEquals($testStatus, $response->getStatus());
        $this->assertEquals($testDuration, $response->getDuration());

        $this->assertFalse($response->getContentType());
        $this->assertTrue($response->getRequest() instanceof \whm\Smoke\Http\Request);
        $this->assertEquals($testRequest, $response->getRequest());
    }

    public function testContentTypeHeader()
    {
        $testBody = '';
        $testHeader = "X-TEST:NO_Content\nCONTENT-TYPE:application/XML\nX-TEST2:NO_CONTENT\n";
        $testStatus = 200;

        $response = new \whm\Smoke\Http\Response($testBody, $testHeader, $testStatus);

        $this->assertEquals('application/xml', $response->getContentType());
    }
}
