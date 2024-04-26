<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler\BitMap;

use Davidyou\BloomFilter\Handler\Traits\RedisTrait;
use InvalidArgumentException;
use Exception;

class RedisBitMap implements BitMapInterface
{
    use RedisTrait;

    protected $length;

    protected $data;

    protected $key;
    /**
     * @throws Exception
     */
    public function __construct($key, $length, $config = null)
    {
        $this->connect($config);
        $this->length = $length;
        $this->key = $key;
    }

    /**
     * @param integer $bit set the bit
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function set($bit)
    {
        $this->guardAgainstBounds($bit);

        return $this->redis->setbit($this->key, $bit, 1);
    }

    /**
     * @param integer $bit check if bit is set
     * @return bool
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function get($bit)
    {
        $this->guardAgainstBounds($bit);

        return $this->redis->getBit($this->key, $bit);
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
