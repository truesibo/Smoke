<?php

namespace whm\Smoke\Http;

class Response
{
    private $status;
    private $body;
    private $duration;
    private $header;
    private $request;

    public function __construct($body, $header, $status, $duration = null, Request $request = null)
    {
        $this->body = $body;
        $this->header = $header;
        $this->status = $status;
        $this->duration = $duration;
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
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

        preg_match('/(^|\n)content-type:(.*?)(;|\n|$)/im', $header, $matches);

        if (!array_key_exists(2, $matches)) {
            return false;
        } else {
            return preg_replace('/[^A-Za-z0-9\-\/]/', '', $matches[2]);
        }
    }
}
