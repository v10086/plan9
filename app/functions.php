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


//以默认配置创建新的数据库C端实例 并执行sql
function dbexec($sql, $params = [], $db = null)
{
    if ($db == null) {
        $db = dbnew(config('database.mysql.default'));
    }
    $sth = @$db->prepare($sql);
    $sth->execute($params);
    if (strtoupper(substr(trim($sql), 0, 6)) == 'SELECT') {
        $resp = $sth->fetchAll();
    } else {
        $resp = $sth->rowCount();
    }
    //资源释放
    $sth = null;
    $db = null;
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
