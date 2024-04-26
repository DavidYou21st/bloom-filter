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

class BitMapHandler implements BloomFilterInterface
{
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
     * @param string $key
     * @param float $errorRate 容错率取值范围 range:(0,1)
     * @param int $capacity 容量
     *
     */
    public function __construct($key, $errorRate, $capacity)
    {
        $this->reserve($key, $errorRate, $capacity);
    }

    /**
     * @param BitMapInterface $bitMap
     * @return BitMapHandler
     */
    public function switch($bitMap)
    {
        $this->bitMap = $bitMap;
        return $this;
    }

    /**
     * @param string $key
     * @param float $errorRate 容错率取值范围 range:(0,1)
     * @param int $capacity 容量
     * @return bool
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

        return true;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function add($value)
    {
        foreach ($this->multiHash->hash($value) as $bit) {
            $this->bitMap->set($bit);
        }
        return true;
    }

    /**
     * @param array $values
     *
     * @return array
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
     *
     * @return bool
     */
    public function exists($value)
    {
        foreach ($this->multiHash->hash($value) as $bit) {
            if (!$this->bitMap->get($bit)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array $values
     *
     * @return array
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