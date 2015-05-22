<?php

namespace whm\Smoke\Config;

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

    private $rules = [];

    public function __construct(array $configArray)
    {
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

        $this->initRules($configArray['rules']);
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
        $rules = [];

        if (count($ruleConfig) === 0) {
            $rules[] = new MaxAgeRule();
            $rules[] = new PragmaNoCacheRule();
            $rules[] = new ExpiresRule();
            $rules[] = new SuccessStatusRule();
            $rules[] = new ClosingHtmlTagRule();
            $rules[] = new DurationRule();
            $rules[] = new SizeRule();
            $rules[] = new \whm\Smoke\Rules\Image\SizeRule();
            $rules[] = new GZipRule();

            foreach ($rules as $rule) {
                if (method_exists($rule, 'init')) {
                    $rule->init();
                }
            }
        } else {
            foreach ($ruleConfig as $name => $ruleElement) {
                $class = $ruleElement['class'];
                $rule  = new $class();

                if (array_key_exists('parameters', $ruleElement)) {
                    if (method_exists($rule, 'init')) {
                        NamedParameters::call([$rule, 'init'],
                            NamedParameters::normalizeParameters($ruleElement['parameters']));
                    }
                } else {
                    $rule->init();
                }
                $rules[] = $rule;
            }
        }
        $this->rules = $rules;
    }

    public function getRules()
    {
        return $this->rules;
    }
}