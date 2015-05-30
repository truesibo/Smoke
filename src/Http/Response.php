<?php

namespace whm\Smoke\Http;

use Ivory\HttpAdapter\Message\Request;

class Response extends \Ivory\HttpAdapter\Message\Response
{
    private $contents;

    public function getStatus()
    {
        return $this->getStatusCode();
    }

    public function getContentType()
    {
        $exploded = explode(';', $this->hasHeader('Content-Type') ? $this->getHeader('Content-Type')[0] : []);

        return array_shift($exploded);
    }

    public function getUri()
    {
        return (string)$this->getParameters()['request']->getUri();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->getParameters()['request'];
    }

    public function getDuration()
    {
        //TODO fix dis
        //return strtotime($this->getHeader('Date')[0]) - strtotime($this->getRequest())
        return 0;
    }

    public function getBody()
    {
        if (!$this->contents) {
            $contents = parent::getBody()->getContents();

            if (false !== $content = @gzdecode($contents)) {
                $contents = $content;
            }

            $this->contents = $contents;
        }

        return $this->contents;
    }
}
