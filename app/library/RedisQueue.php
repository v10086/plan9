<?php

namespace library;

// 基于 Redis 的任务队列（仅对命名进行优化，不改变逻辑）
class RedisQueue
{
    // 保留向后兼容的静态属性名（也可以通过注入替换）
    public static $redisHandler = null; // redis 操作句柄，默认为 null

    // 入队（保持原名，外部可能有调用）
    public static function push($queue, $item)
    {
        self::$redisHandler->sadd('queue', $queue);
        $length = self::$redisHandler->rpush('queue:' . $queue, $item);
        if ($length < 1) {
            return false;
        }
        return true;
    }

    // 出队（保持原名）
    public static function pop($queue)
    {
        $item = self::$redisHandler->lpop('queue:' . $queue);
        if (!$item) {
            return;
        }
        return $item;
    }

    // 返回队列列表（保留原 queues() 方法以兼容旧调用）
    public static function queues()
    {
        return self::getQueues();
    }

    // 更语义化的方法名：获取所有队列名
    public static function getQueues()
    {
        $queues = self::$redisHandler->smembers('queue');
        if (!is_array($queues)) {
            $queues = [$queues];
        }
        return $queues;
    }
}
