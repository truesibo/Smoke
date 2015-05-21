<?php
/**
 * Created by PhpStorm.
 * User: langn
 * Date: 21.05.15
 * Time: 09:47
 */

namespace whm\Smoke\Config;

use whm\Smoke\Rules\Html\SizeRule;
use whm\Smoke\Rules\Http\Header\Cache\ExpiresRule;
use whm\Smoke\Rules\Http\Header\Cache\MaxAgeRule;
use whm\Smoke\Rules\Http\Header\Cache\PragmaNoCacheRule;
use whm\Smoke\Rules\Http\Header\GZipRule;
use whm\Smoke\Rules\Http\Header\SuccessStatusRule;
use whm\Smoke\Rules\Html\ClosingHtmlTagRule;
use whm\Smoke\Rules\Http\DurationRule;

class Configuration
{

    private $blacklist;
    private $whitelist;

    public function __construct(array $configArray)
    {
        if (array_key_exists("blacklist", $configArray)) {
            $this->blacklist = $configArray["blacklist"];
        } else {
            $this->blacklist = "";
        }

        if (array_key_exists("whitelist", $configArray)) {
            $this->whitelist = $configArray["whitelist"];
        } else {
            $this->whitelist = "^^";
        }
    }

    public function getBlacklist()
    {
        return $this->blacklist;
    }

    public function getWhitelist()
    {
        return $this->whitelist;
    }

    public function getRules()
    {
        $rules = array();

        $rules[] = new MaxAgeRule();
        $rules[] = new PragmaNoCacheRule();
        $rules[] = new ExpiresRule();
        $rules[] = new SuccessStatusRule();
        $rules[] = new ClosingHtmlTagRule();
        $rules[] = new DurationRule(1000);
        $rules[] = new SizeRule(200);
        $rules[] = new \whm\Smoke\Rules\Image\SizeRule(50);
        $rules[] = new GZipRule();

        return $rules;
    }
}