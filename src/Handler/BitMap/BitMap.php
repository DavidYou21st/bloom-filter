<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler\BitMap;

use InvalidArgumentException;
class BitMap implements BitMapInterface
{
    /**
     * @var int 对 key 所储存的字符串长度
     */
    protected $length;

    /**
     * @var string 对 key 所储存的字符串值
     */
    protected $data;

    public function __construct($length)
    {
        $this->length = $length;
        $this->data = str_repeat(chr(0), ceil($this->length / 8));
    }

    /**
     * @param integer $bit set the bit
     * @throws InvalidArgumentException
     */
    public function set($bit)
    {
        $this->guardAgainstBounds($bit);

        $index = (int)($bit / 8);

        $this->data[$index] = chr(ord($this->data[$index]) | (1 << $bit % 8));
    }

    /**
     * @param integer $bit get the bit
     * @return bool
     * @throws InvalidArgumentException
     */
    public function get($bit)
    {
        $this->guardAgainstBounds($bit);

        $index = (int)($bit / 8);

        return (ord($this->data[$index]) & (1 << $bit % 8)) === (1 << $bit % 8) ;
    }

    /**
     * @param $bit
     * @throws InvalidArgumentException
     */
    protected function guardAgainstBounds($bit)
    {
        if ($bit < 0 || $bit >= $this->length || intval($bit) !== $bit) {
            throw new InvalidArgumentException('Out of bounds.');
        }
    }

}
