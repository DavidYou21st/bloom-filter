<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler\Hash;

class DJBX33X implements Hash
{

    public function hash($string)
    {
        $hash = 5381;

        foreach (str_split($string) as $chr) {
            $hash = ((($hash << 5) + $hash) ^ ord($chr)) & 0x0ffffffff;
        }

        return $hash & 0x0ffffffff; // limit to 32-bit
    }

}
