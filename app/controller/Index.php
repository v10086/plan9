<?php

namespace controller;

class Index extends \controller\Base
{

    public function index()
    {
        return '首页';
    }

    public function test()
    {
        return [
            'code' => 1,
            'msg' => 'ok'
        ];
    }

    /**
     * testAsyncMysqli
     *
     * 在这里演示并测试 asyncMysqliBatch 的使用：
     * - 读取配置 config('database.mysql.default')
     * - 解析 dsn 得到 host 与 database
     * - 调用 asyncMysqliBatch 发起多个并发查询并返回结果
     */
    public function testAsyncMysqli()
    {
    // 引入工具函数（包含 asyncMysqliBatch）
        $file = FUNS_PATH . '/helper.php';
        if (is_file($file)) {
            include_once $file;
        }

        // 读取数据库配置
        $dbCfg = config('database.mysql.default');
        if (!$dbCfg || !isset($dbCfg['dsn'])) {
            return ['error' => 'database config not found'];
        }

        // 解析 dsn（形式 mysql:host=127.0.0.1;dbname=lumen;...）
        $dsn = $dbCfg['dsn'];
        $host = '127.0.0.1';
        $database = '';
        if (preg_match('/host=([^;]+)/i', $dsn, $m)) {
            $host = $m[1];
        }
        if (preg_match('/dbname=([^;]+)/i', $dsn, $m)) {
            $database = $m[1];
        }

        $mysqliConfig = [
            'host' => $host,
            'user' => $dbCfg['user'] ?? '',
            'password' => $dbCfg['password'] ?? '',
            'database' => $database,
            'port' => $dbCfg['port'] ?? 3306,
            'charset' => $dbCfg['charset'] ?? 'utf8mb4'
        ];

    // 准备若干 sleep 测试 SQL，用于验证并发与超时行为（单位：秒）
    // 这里使用不同的睡眠时长，asyncMysqliBatch 将在多个连接上并发执行它们
        $sqls = [
            'a' => 'SELECT SLEEP(1) AS slept',
            'b' => 'SELECT SLEEP(2) AS slept',
            'c' => 'SELECT SLEEP(3) AS slept',
        ];

        try {
            // pollTimeout 需要大于最大 sleep 时长（此处设为 5 秒）
            $res = asyncMysqliBatch($mysqliConfig, $sqls, 3, 5.0);
        } catch (\Throwable $e) {
            return ['error' => 'exception', 'message' => $e->getMessage()];
        }

        return $res;
    }
}
