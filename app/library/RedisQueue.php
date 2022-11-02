<?php

namespace library;
//基于redis的task队列
class RedisQueue
{
    public static $redisHandler = null; //redis操作句柄 默认为空
    //入列
    public static function push($queue, $item)
    {
        self::$redisHandler->sadd('queue', $queue);
        $length = self::$redisHandler->rpush('queue:' . $queue, $item);
        if ($length < 1) {
            return false;
        }
        return true;
    }
    //出列
    public static function pop($queue)
    {
        $item = self::$redisHandler->lpop('queue:' . $queue);
        if (!$item) {
            return;
        }
        return $item;
    }
    //列表集
    public static function queues()
    {
        $queues = self::$redisHandler->smembers('queue');
        if (!is_array($queues)) {
            $queues[] = $queues;
        }
        return $queues;
    }
}
