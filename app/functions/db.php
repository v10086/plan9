<?php

//数据库操作句柄
function dbexec($sql, $params = [], $db = null)
{
    static $defaultDb = null;
    // --- 新增：强制关闭功能 ---
    // 如果传入特定的指令（例如字符串 'defaultDbClose'），则清空连接并返回
    if ($sql === 'defaultDbClose') {
        $defaultDb = null;// PDO 对象设为 null 会自动断开 TCP 连接
        return true;
    }
    // --- 新增指令：获取最后插入ID ---
    if ($sql === 'lastInsertId' &&  $db === null) {
        return $defaultDb ? $defaultDb->lastInsertId() : 0;
    }

    $defaultDbRetry = false;//是否断开重连
    if ($db === null) {
        if ($defaultDb === null) {
            $defaultDb = dbnew(config('database.mysql.default'));
        }
        $db = $defaultDb;
        $defaultDbRetry = true; //使用默认数据库配置时，开启断开重连功能
    }

    try {
        $sth = $db->prepare($sql);
        $sth->execute($params);
    } catch (\PDOException $e) {
        $errCode = $e->errorInfo[1] ?? 0;
        // 增加重连逻辑
        if (($errCode == 2006 || $errCode == 2013) && $defaultDbRetry) {
            try {
                $defaultDb = dbnew(config('database.mysql.default'));
                $db = $defaultDb;
                $sth = $db->prepare($sql);
                $sth->execute($params);
            } catch (\Exception $retryException) {
                // 如果重连后执行依然失败，则彻底放弃并清理，防止下次请求使用损坏的句柄
                $defaultDb = null;
                throw $retryException;
            }
        } else {
            throw $e;
        }
    }

    // 判断 SQL 类型（使用更高效的 stripos）
    if (stripos(ltrim($sql), 'SELECT') === 0) {
        $resp = $sth->fetchAll();
    } else {
        $resp = $sth->rowCount();
    }
    
    $sth = null; 
    return $resp;
}


//创建数据库实例
function dbnew($config)
{
    $db =  new \PDO($config['dsn'], $config['user'], $config['password']);
    $db->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
    $db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(\PDO::ATTR_EMULATE_PREPARES, FALSE); //数据库使用真正的预编译  
    return $db;
}