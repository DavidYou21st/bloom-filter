<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler\Hash;

class MultiHash
{

    protected $hashOne;

    protected $hashTwo;

    protected $upperBound = 0x0ffffffff;

    protected $hashCount = 3;

    public function __construct(Hash $hashOne, Hash $hashTwo)
    {
        $this->hashOne = $hashOne;
        $this->hashTwo = $hashTwo;
    }

    public function setUpperBound($upperBound)
    {
        $this->upperBound = $upperBound;
    }

    public function setHashCount($hashCount)
    {
        $this->hashCount = $hashCount;
    }

    /**
     * @param $string
     * @return array
     */
    public function hash($string)
    {
        $hashes = [];

        $a = $this->hashOne->hash($string);
        $b = $this->hashTwo->hash($string);

        for ($i = 1; $i <= $this->hashCount; $i++) {
            $hashes[] = ($a + $b * $i) % $this->upperBound;
        }

        return $hashes;
    }

}
