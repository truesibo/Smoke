<?php

namespace whm\Smoke\Scanner;

use phmLabs\Base\Www\Uri;

class PageContainer
{
    private $currentElements = [];
    private $allElements     = [];
    private $parents         = [];

    private $maxSize;

    public function __construct($maxSize = 100)
    {
        $this->maxSize = $maxSize;
    }

    public function getMaxSize()
    {
        return $this->maxSize;
    }

    public function getAllElements()
    {
        return $this->allElements;
    }

    public function push(Uri $uri, Uri $parentUri)
    {
        $uriString = $uri->toString();

        if (count($this->allElements) < $this->maxSize) {
            if (!array_key_exists($uriString, $this->allElements)) {
                $this->allElements[$uriString] = $parentUri->toString();
                array_unshift($this->currentElements, $uri);
            }
        }
    }

    public function pop($count = 1)
    {
        $elements = [];

        for ($i = 0; $i < $count; ++$i) {
            $element = array_pop($this->currentElements);
            if (!is_null($element)) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    public function getParent(Uri $uri)
    {
        return isset($this->allElements[$uri->toString()]) ? $this->allElements[$uri->toString()] : null;
    }
}
