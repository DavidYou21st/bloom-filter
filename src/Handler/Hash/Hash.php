<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler\Hash;

interface Hash
{
    /**
     * @param $string
     * @return integer
     */
    public function hash($string);
}
