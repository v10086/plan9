<?php

//
// [http 代理]
// @param  string       $url     [接口地址]
// @param  array        $params  [数组]
// @param  string       $method  [GET\POST\DELETE\PUT]
// @param  array        $header  [HTTP头信息]
// @param  integer      $timeout [超时时间]
// @return [type]                [接口返回数据]
///
function httpProxy($url, $params = '', $method = 'GET', $header = array(), $timeout = 20000)
{
    // POST 提交方式的传入 $set_params 必须是字符串形式
    $opts = array(
        //        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_NOSIGNAL => 1,    //注意，毫秒超时一定要设置这个  
        CURLOPT_TIMEOUT_MS => $timeout, //超时时间200毫秒 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSLVERSION => 1,
        CURLOPT_HTTPHEADER => $header,
        CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
        CURLOPT_PROXYUSERPWD => "用户名:密码",
        CURLOPT_PROXY => '代理服务器ip',
        CURLOPT_PROXYPORT => '代理服务器端口号',
        CURLOPT_PROXYTYPE => CURLPROXY_SOCKS5_HOSTNAME,
    );
    if (is_array($params)) {
        $params = http_build_query($params);
    }
    //根据请求类型设置特定参数
    switch (strtoupper($method)) {
        case 'GET':
            if ($params) {
                $url = $url . '?' . $params;
            }
            $opts[CURLOPT_URL] = $url;
            break;
        case 'POST':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'DELETE':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_HTTPHEADER] = array("X-HTTP-Method-Override: DELETE");
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'PUT':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 0;
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new \Exception('不支持的请求方式！');
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    return $data;
}
//
// [http 调用接口函数]
// @param  string       $url     [接口地址]
// @param  array        $params  [数组]
// @param  string       $method  [GET\POST\DELETE\PUT]
// @param  array        $header  [HTTP头信息]
// @param  integer      $timeout [超时时间]
// @return [type]                [接口返回数据]
///
function http($url, $params = '', $method = 'GET', $header = array(), $timeout = 20000)
{
    // POST 提交方式的传入 $set_params 必须是字符串形式
    $opts = array(
        //        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_NOSIGNAL => 1,    //注意，毫秒超时一定要设置这个  
        CURLOPT_TIMEOUT_MS => $timeout, //超时时间200毫秒 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSLVERSION => 1,
        CURLOPT_HTTPHEADER => $header
    );

    if (is_array($params)) {
        $params = http_build_query($params);
    }
    //根据请求类型设置特定参数
    switch (strtoupper($method)) {
        case 'GET':
            if ($params) {
                $url = $url . '?' . $params;
            }
            $opts[CURLOPT_URL] = $url;
            break;
        case 'POST':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'DELETE':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_HTTPHEADER] = array("X-HTTP-Method-Override: DELETE");
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'PUT':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 0;
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new \Exception('不支持的请求方式！');
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    return $data;
}

//
// [http curl句柄设置并返回]
// @param  string       $url     [接口地址]
// @param  array        $params  [数组]
// @param  string       $method  [GET\POST\DELETE\PUT]
// @param  array        $header  [HTTP头信息]
// @param  integer      $timeout [超时时间]
// @return [type]                [返回curl操作句柄]
//
function getCurl($url, $params = '', $method = 'GET', $header = array(), $timeout = 20000)
{
    // POST 提交方式的传入 $set_params 必须是字符串形式
    $opts = array(
        //        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_NOSIGNAL => 1,    //注意，毫秒超时一定要设置这个  
        CURLOPT_TIMEOUT_MS => $timeout, //超时时间200毫秒 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSLVERSION => 1,
        CURLOPT_HTTPHEADER => $header
    );

    if (is_array($params)) {
        $params = http_build_query($params);
    }
    /* 根据请求类型设置特定参数 */
    switch (strtoupper($method)) {
        case 'GET':
            if ($params) {
                $url = $url . '?' . $params;
            }
            $opts[CURLOPT_URL] = $url;
            break;
        case 'POST':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'DELETE':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_HTTPHEADER] = array("X-HTTP-Method-Override: DELETE");
            $opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        case 'PUT':
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 0;
            $opts[CURLOPT_CUSTOMREQUEST] = 'PUT';
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
            throw new \Exception('不支持的请求方式！');
    }

    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    return $ch;
    // $data = curl_exec($ch);
    // $error = curl_error($ch);

}

//http 批量请求 
function httpMulti($nodes)
{
    $mh = curl_multi_init();
    foreach ($nodes as $i => $curl) {
        curl_multi_add_handle($mh, $curl);
    }
    $running = NULL;
    do {
        usleep(1000);
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    $res = array();
    foreach ($nodes as $i => $curl) {
        $res[$i] = curl_multi_getcontent($curl);
    }

    foreach ($nodes as $i => $curl) {
        curl_multi_remove_handle($mh, $curl);
    }
    curl_multi_close($mh);
    return $res;
}

//读取配置文件
function config($params)
{
    $config = explode('.', $params);
    if (!isset($GLOBALS['configs']) || !isset($GLOBALS['configs'][$config[0]])) {
        $GLOBALS['configs'][$config[0]] = require(CONF_PATH . DS . $config[0] . '.php');
    }
    $value = $GLOBALS['configs'][$config[0]];
    for ($i = 1; $i < sizeof($config); $i++) {
        if (!isset($value[$config[$i]])) {
            return FALSE;
        }
        $value = $value[$config[$i]];
    }
    return $value;
}
//string $source 必需。规定要复制的文件
//string $destination  写入目标
//string $name 文件名
function fileSave($source, $destination, $name)
{
    // 自动创建日志目录
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    return copy($source, $destination . DS . $name);
}

// 日志写入接口已移除：建议使用 Monolog 或其他成熟日志库进行分级与管理。



//加密
//OPENSSL_NO_PADDING 会影响到 PHP setcookie
function encrypt($data, $key)
{
    return base64_encode(openssl_encrypt($data, 'BF-ECB', $key, OPENSSL_RAW_DATA));
}

//解密
//OPENSSL_NO_PADDING 会影响到 PHP setcookie
function decrypt($data, $key)
{
    return openssl_decrypt(base64_decode($data), 'BF-ECB', $key, OPENSSL_RAW_DATA);
}


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


/**
 * asyncMysqliBatch
 *
 * 使用多个 mysqli 连接并发执行多条 SQL（基于 mysqli 的异步查询 MYSQLI_ASYNC）。
 * 注意：单个 mysqli 连接上只能同时保有一个未完成的异步查询，因此需要为并发的 SQL 创建多个连接。
 *
 * 参数说明：
 *  - $dbConfig: 数组，数据库连接配置，至少包含 host,user,password,database，可选 port,charset
 *  - $sqls: 数组，key => sql 的形式（可以是关联数组），返回结果会按相同 key 返回
 *  - $concurrency: 并发连接数上限，默认等于 SQL 数量
 *  - $pollTimeout: poll 的超时时间（秒，可带小数），当轮询无响应时会以该时间为单次等待
 *
 * 返回：按输入 $sqls 的键返回结果数组，每个值为：
 *  [
 *    'success' => true|false,
 *    'type' => 'select'|'modify'|'unknown',
 *    'data' => array|null, // select 的 fetch_all 或 modify 的受影响行数
 *    'error' => string|null
 *  ]
 */
function asyncMysqliBatch(array $dbConfig, array $sqls, int $concurrency = null, float $pollTimeout = 1.0)
{
    if (count($sqls) === 0) {
        return [];
    }

    $keys = array_keys($sqls);
    $total = count($keys);
    $concurrency = $concurrency ?: $total;
    $workers = min($concurrency, $total);

    // 建立 worker 连接
    $conns = [];
    for ($i = 0; $i < $workers; $i++) {
        $mysqli = new mysqli(
            $dbConfig['host'] ?? '127.0.0.1',
            $dbConfig['user'] ?? 'root',
            $dbConfig['password'] ?? '',
            $dbConfig['database'] ?? '',
            $dbConfig['port'] ?? 3306
        );
        if ($mysqli->connect_errno) {
            // 关闭已打开的连接
            foreach ($conns as $c) {
                @$c->close();
            }
            throw new \RuntimeException('Connect error: ' . $mysqli->connect_error);
        }
        $mysqli->set_charset($dbConfig['charset'] ?? 'utf8mb4');
        $conns[] = $mysqli;
    }

    // 准备队列（保留原始键）
    $queue = [];
    foreach ($sqls as $k => $sql) {
        $queue[] = ['key' => $k, 'sql' => $sql];
    }

    $results = [];
    foreach ($keys as $k) {
        $results[$k] = null;
    }

    $assigned = []; // connIndex => ['key'=>..., 'sql'=>...]

    // 初次分配
    for ($i = 0; $i < $workers; $i++) {
        if (count($queue) === 0) break;
        $job = array_shift($queue);
        $conns[$i]->query($job['sql'], MYSQLI_ASYNC);
        $assigned[$i] = $job;
    }

    // poll 循环
    $micro = (int)(($pollTimeout - floor($pollTimeout)) * 1000000);
    $sec = (int)floor($pollTimeout);

    while (count($assigned) > 0) {
        $read = $error = $reject = [];
        foreach (array_keys($assigned) as $ci) {
            $read[] = $conns[$ci];
        }

        $n = mysqli_poll($read, $error, $reject, $sec, $micro);

        if ($n === false) {
            // poll 失败，记录错误并退出
            foreach ($assigned as $ci => $job) {
                $results[$job['key']] = [
                    'success' => false,
                    'type' => 'unknown',
                    'data' => null,
                    'error' => 'poll failed'
                ];
            }
            break;
        }

        if ($n === 0) {
            // 超时一次：继续循环或认定为超时错误
            // 为了避免无限循环，当没有任何响应且队列为空时，认定为超时错误
            // 这里选择将仍在等待的任务标记为超时并退出
            foreach ($assigned as $ci => $job) {
                $results[$job['key']] = [
                    'success' => false,
                    'type' => 'unknown',
                    'data' => null,
                    'error' => 'timeout'
                ];
            }
            break;
        }

        // 处理有响应的连接
        foreach ($read as $r) {
            // 找到对应的 connection index
            $ci = null;
            foreach ($assigned as $idx => $job) {
                if ($conns[$idx] === $r) {
                    $ci = $idx;
                    break;
                }
            }
            if ($ci === null) continue;

            $job = $assigned[$ci];

            // 取回结果
            $res = $r->reap_async_query();
            if ($res === false) {
                $results[$job['key']] = [
                    'success' => false,
                    'type' => 'unknown',
                    'data' => null,
                    'error' => $r->error
                ];
            } else {
                if ($res instanceof mysqli_result) {
                    $data = $res->fetch_all(MYSQLI_ASSOC);
                    $res->free();
                    $results[$job['key']] = [
                        'success' => true,
                        'type' => 'select',
                        'data' => $data,
                        'error' => null
                    ];
                } elseif ($res === true) {
                    // 非 SELECT 语句（insert/update/delete）
                    $results[$job['key']] = [
                        'success' => true,
                        'type' => 'modify',
                        'data' => $r->affected_rows,
                        'error' => null
                    ];
                } else {
                    $results[$job['key']] = [
                        'success' => false,
                        'type' => 'unknown',
                        'data' => null,
                        'error' => 'unknown result type'
                    ];
                }
            }

            // 当前连接如果还有队列任务，则继续发起下一条异步查询；否则取消分配
            if (count($queue) > 0) {
                $next = array_shift($queue);
                $r->query($next['sql'], MYSQLI_ASYNC);
                $assigned[$ci] = $next;
            } else {
                unset($assigned[$ci]);
            }
        }
    }

    // 关闭连接
    foreach ($conns as $c) {
        @$c->close();
    }

    return $results;
}
