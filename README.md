# CacheWatch

CacheWatch is a tool that can be used to check the cache header of a given website. 

The only thing you have to do is to define the entry point for the scanner and start. The CacheWatch crawler will scan the first n (defined as command line parameter) websites it finds abd checks it agains a group of defined rules. 

```
php bin/CacheWatch.php analyse --num_urls=200 "http://www.amilio.de"
```

## Installation

### Phar Archive

Download the phar archive using curl
```
curl -O -LSs https://raw.github.com/phmLabs/CacheWatch/master/bin/CacheWatch.phar && chmod +x CacheWatch.phar
```

### Soruce Code
Download the sources from GitHub.
```
git clone https://github.com/phmLabs/CacheWatch.git CacheWatch
```

Run composer
```
php composer.phar install
```

Installation complete.

## Configuration

### Command Line Parameters

* --num_urls - This parameter defines how many urls should be checked
* --parallel_requests - How many request should be don in parallel
* --config_file - Config file containing a black- and whitelist

### Config File

The config file is used to define a whitelist and a blacklist for the run. It is stored in the yaml file format.

Every element in the lists will be handled as a regular expression.

*Example amilio.yml* 
```
whitelist:
 - ^www.amilio.de^
 
blacklist: 
 - ^www.amilio.de/api^
```
