<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler;

use Davidyou\BloomFilter\Handler\BitMap\BitMapInterface;
use Davidyou\BloomFilter\Handler\Hash\DJBX33X;
use Davidyou\BloomFilter\Handler\Hash\FNVHash;
use Davidyou\BloomFilter\Handler\Hash\MultiHash;
use Davidyou\BloomFilter\Handler\Traits\RedisTrait;
use Exception;
use Redis;

class RedisBitMapHandler implements BloomFilterInterface
{
    use RedisTrait;

    /**
     * @var BitMapInterface
     */
    private $bitMap;
    /**
     * @var MultiHash
     */
    private $multiHash;
    /**
     * @var string
     */
    private $key;
    /**
     * @var int
     */
    private $capacity;
    /**
     * @var float
     */
    private $errorRate;

    /**
     * @param array|null|Redis $config
     * @throws Exception
     */
    public function __construct($key, $errorRate, $capacity, $config = null)
    {
        if ($config instanceof Redis) {
            $this->setInstance($config);
        } else {
            $this->connect($config);
        }

        $this->reserve($key, $errorRate, $capacity);
    }

    /**
     * @param string $key
     * @param float $errorRate
     * @param int $capacity
     *
     */
    public function reserve($key, $errorRate, $capacity)
    {
        $this->key = $key;
        $this->errorRate = $errorRate;
        $this->capacity = $capacity;
        // 计算bitmap大小和hash函数个数
        $sizeOfBitMap = (int)ceil(($capacity * log($errorRate)) / log(1 / (pow(2, log(2)))));
        $hashFunctions = (int)($sizeOfBitMap / $capacity * log(2));
        // 初始化hash函数
        $this->multiHash = new MultiHash(new DJBX33X(), new FNVHash());
        $this->multiHash->setUpperBound($sizeOfBitMap);
        $this->multiHash->setHashCount($hashFunctions);
    }

    /**
     * @param string $value
     *
     * @return bool
     * @throws Exception
     */
    public function add($value)
    {
        $pipe = $this->redis->multi();

        foreach ($this->multiHash->hash($value) as $bit) {
            $pipe->setbit($this->key, $bit, 1);
        }

        return $pipe->exec();
    }

    /**
     * @param array $values
     * @return array
     * @throws Exception
     */
    public function mAdd($values)
    {
        $result = [];
        foreach ($values as $value) {
            $result[] = $this->add($value);
        }
        return $result;
    }

    /**
     * @param string $value
     * @return bool
     * @throws Exception
     */
    public function exists($value)
    {
        $pipe = $this->redis->multi();

        foreach ($this->multiHash->hash($value) as $bit) {
            $pipe->getbit($this->key, $bit);
        }

        $results = $pipe->exec();

        foreach ($results as $bit) {
            if (0 == $bit) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $values
     *
     * @return array
     * @throws Exception
     */
    public function mExists($values)
    {
        $result = [];
        foreach ($values as $value) {
            $result[] = $this->exists($value);
        }
        return $result;
    }
}
