# CacheWatch

CacheWatch is a tool that can be used to check the cache header of a given website. 

The only thing you have to do is to define the entry point for the scanner and start. The CacheWatch crawler will scan the first n (defined as command line parameter) websites it finds abd checks it agains a group of defined rules. 

```
php bin/CacheWatch.php analyse --num_urls=200 "http://www.amilio.de"
```
