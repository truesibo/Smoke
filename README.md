# Smoke

- License: MIT
- Build: [![Build Status](https://secure.travis-ci.org/phmLabs/Smoke.png)](http://travis-ci.org/phmLabs/Smoke)
- Homepage: [http://www.thewebhatesme.com/](http://www.thewebhatesme.com/)

Smoke can be used to check if a web platform is basically working.

The only thing you have to do is to define the entry point for the scanner and start. The Smoke crawler will scan the first n (defined as command line parameter) websites it finds and check them against a group of defined rules.

```
Smoke.phar analyse "http://www.amilio.de"
```

## Installation

Download the phar archive using curl
```
curl -O -LSs https://raw.github.com/phmLabs/Smoke/master/bin/Smoke.phar && chmod +x Smoke.phar
```

Installation complete.

## Configuration

### Command Line Parameters

* --num_urls - This parameter defines how many urls should be checked (standard: 20)
* --parallel_requests - How many request should be don in parallel (standard: 10)
* --config_file - Config file containing a black- and whitelist

### Config File

You may configure any Smoke run using URL *whitelists* and *blacklists* as well as ruleset *definitions*. The configuration is stored in a singe YAML file, so it can be used in subsequent test runs. The configuration file may contain up to three elements:

- **whitelist** contains a dash list of URL regex patterns. This allows Smoke to test an URL in question when it is found somewhere in the HTML source.

- **blacklist** *optional* – contains a dash list of URL regex patterns Smoke is not allowed to test. This is useful for any linked third-party domains or services that are a project of their own, e.g. online shops on a subdomain. 

- **rules** *optional* – contains a list of named tests with their corresponding PHP classes and test parameters.

Currently, it is not possible to *blacklist* a certain URL pattern and *whitelist* another that matches a blacklisted one.

####Configuration example and usage
 
```yaml
whitelist:
 - ^www.amilio.de^
 
blacklist: 
 - ^www.amilio.de/api^

rules:
  HtmlSize:
    class: whm\Smoke\Rules\Html\SizeRule
    parameters:
      - maxSize: 1

  ImageSize:
      class: whm\Smoke\Rules\Image\SizeRule
      parameters:
        - maxSize: 1
```

For more examples, see the *examples* directory. 
To call Smoke with your config file, just issue on command line:

```bash
Smoke.phar analyse --config_file="test.yml" test.com
```



