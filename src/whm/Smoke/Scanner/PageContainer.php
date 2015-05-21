<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 19.05.15
 * Time: 09:14
 */

namespace whm\Smoke\Scanner;


use phmLabs\Base\Www\Uri;

class PageContainer
{

    private $currentElements = array();
    private $allElements = array();
    private $parents = array();

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
        $elements = array();

        for($i = 0; $i < $count; $i++) {
            $element = array_pop($this->currentElements);
            if( !is_null($element)) {
                $elements[] = $element;
            }
        }

        return $elements;
    }

    public function getParent(Uri $uri)
    {
        return $this->allElements[$uri->toString()];
    }
}