<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $testBody = 'TestBodyWith<strong>special</strong>Chäräctörs';
        $testHeader = 'Test Header ';
        $testStatus = 750;
        $testDuration = 300;

        $response = new \whm\Smoke\Http\Response($testBody, $testHeader, $testStatus, $testDuration);

        $this->assertEquals($testBody,      $response->getBody());
        $this->assertEquals($testHeader,    $response->getHeader());
        $this->assertEquals("testheader",   $response->getHeader(true));
        $this->assertEquals($testStatus,    $response->getStatus());
        $this->assertEquals($testDuration,  $response->getDuration());

        $this->assertFalse($response->getContentType());
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
