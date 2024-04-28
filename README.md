# A Bloom Filter for PHP #

## Install
composer require davidyou/bloom-filter

Or put the following in your package.json:
```javascript
{
    "require": {
        "davidyou/bloom-filter": "*"
    }
}
```
then run `composer install`.

## Requirements
redis version >= 4.0
php version >= 7.1.8  
Installed plug-ins [RedisBloom](https://github.com/RedisBloom/RedisBloom)

## How to install RedisBloom plugin ?
git clone https://github.com/RedisBloom/RedisBloom.git  
mv RedisBloom /usr/local  
cd RedisBloom  
make

vi /etc/redis/redis.conf  
loadmodule /usr/local/RedisBloom/redisbloom.so INITIAL_SIZE 1000 ERROR_RATE 0.001


## Usage ##
### 纯PHP实现布隆过滤器 ###
```php
<?php

require_once('vendor/autoload.php');

use Davidyou\BloomFilter\BloomFilter;
use Davidyou\BloomFilter\Handler\BitMapHandler;
use Davidyou\BloomFilter\Handler\BitMap\BitMap;

$errorRate = 0.001;//容错率取值
$capacity = 10000; //容量
$handler = new BitMapHandler('user', $errorRate, $capacity);
$handler->switch(new BitMap($capacity));//指定使用 bitmap，或其它实现了BitMapInterface接口的类

$bloomFilter = new BloomFilter($handler);

$bloomFilter->add('li ming');
$bloomFilter->add('yang li');
// ... add more

$bloomFilter->exists('yang li'); // true - 可能存在
$bloomFilter->exists('lili'); // false - 绝对不存在
```
### PHP+Redis的BitMap做布隆过滤器 ###
```php
<?php
use Davidyou\BloomFilter\BloomFilter;
use Davidyou\BloomFilter\Handler\RedisBitMapHandler;

$capacity = 100000;
$errorRate = 0.001;

$handler = new RedisBitMapHandler('user', $errorRate, $capacity, ['host'=>'192.168.111.71']);

$bloomFilter = new BloomFilter($handler);

$bloomFilter->add('item1');
$bloomFilter->add('item2');
$bloomFilter->add('item3');

$bloomFilter->exists('item1'); // true
$bloomFilter->exists('item5'); // false
$bloomFilter->exists('item3'); // true

// The following call will return false with a 0.1% probability of
// being true as long as the amount of items in the filter are < 100000
$bloomFilter->exists('non-existing-item'); // false
```

### PHP+Redis自身的布隆过滤器组件 ###
```php
<?php
use Davidyou\BloomFilter\BloomFilter;
use Davidyou\BloomFilter\Handler\RedisHandler;

$capacity = 100000;
$errorRate = 0.001;

$handler = new RedisHandler(['host' => '127.0.0.1', 'part'=>6380, 'auth' => 123456])
$handler->reserve('user', $errorRate, $capacity);

$bloomFilter = new BloomFilter($handler);

$bloomFilter->add('item1');
$bloomFilter->add('item2');
$bloomFilter->add('item3');

$bloomFilter->exists('item1'); // true
$bloomFilter->exists('item2'); // true
$bloomFilter->exists('item3'); // true

// The following call will return false with a 0.1% probability of
// being true as long as the amount of items in the filter are < 100000
$bloomFilter->exists('non-existing-item'); // false

```

## License ##

You can find the license for this code in [the LICENSE file](LICENSE).
