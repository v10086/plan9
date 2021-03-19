<?php
//解决跨域访问的问题
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header( 'Access-Control-Allow-Origin:*' );//可设置特定域名
    header( 'Access-Control-Allow-Credentials:true' );//解决跨域cookie传递限制
    header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header( 'Access-Control-Allow-Headers: Content-Type,X-Requested-With, X-Access-Token, Authorization, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since');
    //跨域响应的时候允许获取的响应头信息
    header( 'Access-Control-Expose-Headers: ETag, Link,X-RateLimit-Limit, X-RateLimit-Remaining, X-RateLimit-Reset, X-OAuth-Scopes, X-Accepted-OAuth-Scopes, X-Poll-Interval');
    header( 'Access-Control-Max-Age: 86400');
    header( 'Content-Type:text/plain charset=UTF-8');
    header( 'Content-Length: 0',true);
    header('status: 204');
    header('HTTP/1.0 204 No Content');
    exit;
} 

define('DS', '/');

define("ROOT_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */

define('APP_NAME',      'app');

define("APP_PATH", ROOT_PATH . DS . APP_NAME ); //应用路径

define("LIB_PATH", ROOT_PATH . DS . APP_NAME . DS . 'library' ); //库路径

define("VIEW_PATH", ROOT_PATH . DS . APP_NAME . DS . 'view' ); //视图库

define("CONF_PATH", ROOT_PATH . DS . 'config' ); //配置文件路径

define("CERT_PATH", ROOT_PATH . DS . 'cert' ); //证书文件路径

define("LOG_PATH", ROOT_PATH . DS . 'log' ); //日志文件路径

define("DATA_PATH",ROOT_PATH  . DS . 'data'); //数据文件存放路径

define("VENDOR_PATH",ROOT_PATH . DS . 'vendor' );//第三方拓展路径

define("API", explode('?', $_SERVER['REQUEST_URI'])[0]);//API路径


require VENDOR_PATH.DS ."autoload.php";//引入第三方类库

require  APP_PATH. DS ."helper.php";
require  APP_PATH. DS ."bootstrap.php";
//自动加载类库
spl_autoload_register(function ($class) {
    $path = APP_PATH;
    foreach (explode('\\', $class) as $key => $value) {
        $path = $path. DS . $value;
    }
    include $path.'.php';
});
try{
    if(isset($_SERVER['CONTENT_TYPE'])){
        $content_type = explode(';',$_SERVER['CONTENT_TYPE']);
        if(in_array('application/json',$content_type)){
            $_POST = json_decode(file_get_contents('php://input'),true);
        }
    }

}catch(\Exception $e){
    echo json_encode(['code'=>500,'msg'=>'参数格式不正确']);exit;
}
//初始化一些必要的类库
//\v10086\DB::$cofing= config('database');
//\v10086\Redis::$cofing= config('redis');

$route= require (CONF_PATH. DS .'route.php');

if(!isset($route[API])){
    header("http/1.1 404 Not Found");
    exit;
}

$route[API] =str_replace("/","\\", $route[API] );
list($class, $func) =explode('@', $route[API]);
$class = new $class;
$res = $class->$func();
if($res!==NULL){
    if(is_array($res) || is_object($res)){
        header('Content-Type:text/json; charset=UTF-8');
        echo json_encode($res);
    }elseif(is_string($res)){
        header('Content-Type:text/html; charset=UTF-8');
        echo $res;
    }
}

    
