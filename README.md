# Smoke

Smoke can be used to check if a web platform is basically working.

The only thing you have to do is to define the entry point for the scanner and start. The Smoke crawler will scan the first n (defined as command line parameter) websites it finds abd checks it agains a group of defined rules.

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

The config file is used to define a whitelist and a blacklist for the run. It is stored in the yaml file format.

Every element in the lists will be handled as a regular expression.

*Example amilio.yml* 
```yaml
whitelist:
 - ^www.amilio.de^
 
blacklist: 
 - ^www.amilio.de/api^
```
