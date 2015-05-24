<?php

namespace whm\Smoke\Config;

use PhmLabs\Base\Www\Uri;
use PhmLabs\Components\NamedParameters\NamedParameters;
use whm\Smoke\Rules\Html\ClosingHtmlTagRule;
use whm\Smoke\Rules\Html\SizeRule;
use whm\Smoke\Rules\Http\DurationRule;
use whm\Smoke\Rules\Http\Header\Cache\ExpiresRule;
use whm\Smoke\Rules\Http\Header\Cache\MaxAgeRule;
use whm\Smoke\Rules\Http\Header\Cache\PragmaNoCacheRule;
use whm\Smoke\Rules\Http\Header\GZipRule;
use whm\Smoke\Rules\Http\Header\SuccessStatusRule;

class Configuration
{
    private $blacklist;
    private $whitelist;

    private $scanForeignDomains = false;

    private $startUri;

    private $containerSize;

    private $parallelRequestCount;

    private $rules = [];

    public function __construct(Uri $uri, array $configArray, array $defaultSettings = array())
    {
        if (count($configArray) == 0) {
            $configArray = $defaultSettings;
        }

        if (array_key_exists('options', $configArray)) {
            if (array_key_exists('extendDefault', $configArray["options"])) {
                if ($configArray["options"]["extendDefault"] === true) {
                    $configArray = array_replace_recursive($defaultSettings, $configArray);
                }

            }
            if (array_key_exists('scanForeignDomains', $configArray['options'])) {
                $this->scanForeignDomains = $configArray["options"]["scanForeignDomains"];
            }
        }

        if (array_key_exists('blacklist', $configArray)) {
            $this->blacklist = $configArray['blacklist'];
        } else {
            $this->blacklist = [];
        }

        if (array_key_exists('whitelist', $configArray)) {
            $this->whitelist = $configArray['whitelist'];
        } else {
            $this->whitelist = ['^^'];
        }

        if (!array_key_exists('rules', $configArray)) {
            $configArray['rules'] = [];
        }

        $this->startUri = $uri;

        $this->initRules($configArray['rules']);
    }

    public function getStartUri()
    {
        return $this->startUri;
    }

    public function enableForeignDomainScan()
    {
        $this->scanForeignDomains = true;
    }

    public function setContainerSize($size)
    {
        $this->containerSize = $size;
    }

    public function getContainerSize()
    {
        return $this->containerSize;
    }

    public function setParallelRequestCount($count)
    {
        $this->parallelRequestCount = $count;
    }

    public function getParallelRequestCount()
    {
        return $this->parallelRequestCount;
    }

    public function getBlacklist()
    {
        return $this->blacklist;
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

    private function initRules($ruleConfig)
    {
        foreach ($ruleConfig as $name => $ruleElement) {
            $class = $ruleElement['class'];
            $rule = new $class();

            if (method_exists($rule, 'init')) {
                if (array_key_exists('parameters', $ruleElement)) {
                    NamedParameters::call([$rule, 'init'], $ruleElement['parameters']);
                } else {
                    $rule->init();
                }
            }
            $this->rules[] = $rule;
        }
    }

    public function getRules()
    {
        return $this->rules;
    }

    public function scanForeignDomains()
    {
        return $this->scanForeignDomains;
    }

    public function isUriAllowed(Uri $uri)
    {
        if (!$this->scanForeignDomains()) {
            if (!$this->startUri->isSameTopLevelDomain($uri)) {
                return false;
            }
        }

        foreach ($this->whitelist as $whitelist) {
            if (preg_match($whitelist, $uri->toString())) {
                foreach ($this->blacklist as $blacklist) {
                    if (preg_match($blacklist, $uri->toString())) {
                        return false;
                    }
                }

                return true;
            }
        }

        return false;
    }
}
