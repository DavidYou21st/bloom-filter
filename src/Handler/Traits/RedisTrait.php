<?php
/**
 * @project   Davidyou BloomFilter
 * @author    Davidyou <davidyou21st@gmail.com>
 * @license   MIT
 * @link      https://github.com/davidyou/bloom-filter
 */

namespace Davidyou\BloomFilter\Handler\Traits;

use Exception;
use Redis;

trait RedisTrait
{
    /** Redis Config */
    private $host = '127.0.0.1';
    private $port = 6379;
    private $timeout = 0.0;
    private $reserved = null;
    private $retry_interval = 0;
    private $read_timeout = 0.0;
    private $auth = null;
    private $database = 0;
    private $pconnect = false;

    protected Redis $redis;

    /**
     * Connect to Redis
     * @param array|null $config
     *
     * @return void
     * @throws Exception
     */
    protected function connect($config = null)
    {
        $this->host = $config['host'] ?? $this->host;
        $this->port = $config['port'] ?? $this->port;
        $this->timeout = $config['timeout'] ?? $this->timeout;
        $this->reserved = $config['reserved'] ?? $this->reserved;
        $this->retry_interval = $config['retry_interval'] ?? $this->retry_interval;
        $this->read_timeout = $config['read_timeout'] ?? $this->read_timeout;
        $this->database = $config['database'] ?? $this->database;
        $this->capacity = $config['capacity'] ?? $this->capacity;
        $this->auth = $config['auth'] ?? $this->auth;
        $this->errorRate = $config['error_rate'] ?? $this->errorRate;
        $this->pconnect = $config['pconnect'] ?? $this->pconnect;

        try {
            $this->redis = new Redis();
            if ($this->pconnect) {
                $this->redis->pconnect($this->host, $this->port, $this->timeout, $this->reserved, $this->retry_interval, $this->read_timeout);
            } else {
                $this->redis->connect($this->host, $this->port, $this->timeout, $this->reserved, $this->retry_interval, $this->read_timeout);
            }

            $this->redis->auth($this->auth);
            $this->redis->select($this->database);
        } catch (Exception $e) {
            throw new Exception("connect to Redis server failed");
        }
    }

    /**
     * @param string $host
     * @param int $port
     * @param int $database
     * @param null $auth
     * @param float $timeout
     * @param null $reserved
     * @param int $retry_interval
     * @param float $read_timeout
     * @param bool $pconnect
     *
     * @return $this
     * @throws Exception
     */
    public function setConfig($host = '127.0.0.1', $port = 6379, $database = 0, $auth = null, $timeout = 0.0, $reserved = null, $retry_interval = 0, $read_timeout = 0.0, $pconnect=false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->auth = $auth;
        $this->database = $database;
        $this->timeout = $timeout;
        $this->reserved = $reserved;
        $this->retry_interval = $retry_interval;
        $this->read_timeout = $read_timeout;
        $this->pconnect = $pconnect;

        $this->connect();

        return $this;
    }

    /**
     * @param Redis $instance
     *
     * @return $this
     * @throws Exception
     */
    public function setInstance($instance)
    {
        if (!$instance instanceof Redis) {
            throw new Exception("instance must be Redis");
        }
        $this->redis = $instance;

        return $this;
    }

}