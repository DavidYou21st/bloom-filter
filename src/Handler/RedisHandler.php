<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler;

use Davidyou\BloomFilter\Handler\Traits\RedisTrait;
use Exception;
use Redis;

class RedisHandler implements BloomFilterInterface
{
    use RedisTrait;

    /** Bloom Filter Commands */
    const BF_RESERVE = 'BF.RESERVE';
    const BF_ADD = 'BF.ADD';
    const BF_MADD = 'BF.MADD';
    const BF_INSERT = 'BF.INSERT';
    const BF_EXISTS = 'BF.EXISTS';
    const BF_MEXISTS = 'BF.MEXISTS';


    /**
     * 容错率取值范围(0,1)
     * @var float
     */
    protected $errorRate = 0.001;

    /**
     * 容量
     * @var int
     */
    protected $capacity;

    /**
     * 操作对象
     * @var string
     */
    protected $key;

    /**
     * @param array|null|Redis $config
     * @throws Exception
     */
    public function __construct($config = null)
    {
        if ($config instanceof Redis) {
            $this->setInstance($config);
        } else {
            $this->connect($config);
        }
    }

    /**
     * @param string $key
     * @param float $errorRate
     * @param int $capacity
     *
     * @return bool
     * @throws Exception
     */
    public function reserve($key, $errorRate, $capacity)
    {
        $this->key = $key;

        $arguments = [$key, $errorRate, $capacity];

        return $this->redis->rawCommand(self::BF_RESERVE, ...$arguments);
    }

    /**
     * @param string $value
     *
     * @return bool
     * @throws Exception
     */
    public function add($value)
    {
        $arguments = [$this->key, $value];

        return $this->redis->rawCommand(self::BF_ADD, ...$arguments);
    }

    /**
     * @param array $value
     *
     * @return array
     * @throws Exception
     */
    public function mAdd($values)
    {
        $arguments = array_merge([$this->key], $values);

        return $this->redis->rawCommand(self::BF_MADD, ...$arguments);
    }

    public function exists($value)
    {
        $arguments = [$this->key, $value];

        return $this->redis->rawCommand(self::BF_EXISTS, ...$arguments);
    }

    public function mExists($values)
    {
        $arguments = array_merge([$this->key], $values);

        return $this->redis->rawCommand(self::BF_MEXISTS, ...$arguments);
    }
}
