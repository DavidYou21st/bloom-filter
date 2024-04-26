<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */
namespace Davidyou\BloomFilter;

use Davidyou\BloomFilter\Handler\BloomFilterInterface;

/**
 * Represents a bloom filter
 */
class BloomFilter
{
    /**
     * @var BloomFilterInterface
     */
    private BloomFilterInterface $handler;

    /**
     * @param BloomFilterInterface $handler
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param string $key
     * @param float $errorRate
     * @param int $capacity
     *
     * @return bool
     */
    public function reserve($key, $errorRate, $capacity)
    {
        return $this->handler->reserve($key, $errorRate, $capacity);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function add($value)
    {
        return $this->handler->add($value);
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function mAdd($values)
    {
        return $this->handler->mAdd($values);
    }

    /**
     * @param $value
     *
     * @return bool
     */
    public function exists($value)
    {
        return $this->handler->exists($value);
    }

    /**
     * @param $values
     *
     * @return array
     */
    public function mExists($values)
    {
        return $this->handler->mExists($values);
    }
}
