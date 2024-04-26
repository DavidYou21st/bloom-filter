<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */
namespace Davidyou\BloomFilter\Handler\BitMap;

/**
 * Represents a bit map
 */
interface BitMapInterface
{
    /**
     * @param int $bit set the bit
     */
    public function set($bit);

    /**
     * @param int $bit get the bit
     */
    public function get($bit);

}
