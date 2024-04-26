<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */
namespace Davidyou\BloomFilter\Handler;

/**
 * Represents a bloom filter
 */
interface BloomFilterInterface
{
    /**
     * 创建一个过滤器
     * @param string $key 操作对象
     * @param float $errorRate 容错率取值范围 range:(0,1)
     * @param int $capacity 容量
     *
     * @return bool
     */
    public function reserve($key, $errorRate, $capacity);

    /**
     *
     * @param string $value
     *
     * @return bool
     */
    public function add($value);

    /**
     * @param array $values
     *
     * @return array
     */
    public function mAdd($values);

    /**
     * @param string $value
     *
     * @return bool
     */
    public function exists($value);

    /**
     * @param array $values
     *
     * @return array
     */
    public function mExists($values);

}
