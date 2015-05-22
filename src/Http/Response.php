<?php

namespace whm\Smoke\Http;

class Response
{
    private $status;
    private $body;
    private $duration;
    private $header;

    public function __construct($body, $header, $status, $duration = null)
    {
        $this->body = $body;
        $this->header = $header;
        $this->status = $status;
        $this->duration = $duration;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * Returns the duration in milliseconds.
     */
    public function getDuration()
    {
        return $this->duration;
    }

    public function getHeader($normalized = false)
    {
        if ($normalized) {
            return strtolower(str_replace(' ', '', $this->header));
        }

        return $this->header;
    }

    public function getContentType()
    {
        $header = $this->getHeader(true);

        // @fixme doesn't work: Content-Type:text/html; charset=UTF-8

        preg_match('^content-type:(.*)^', $header, $matches);
        if (!array_key_exists(1, $matches)) {
            return false;
        } else {
            return $matches[1];
        }
    }
}