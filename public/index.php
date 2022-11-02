<?php

define('DS', '/');

define("ROOT_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */

define('APP_NAME',      'app');

define("APP_PATH", ROOT_PATH . DS . APP_NAME); //应用路径

define("LIB_PATH", ROOT_PATH . DS . APP_NAME . DS . 'library'); //库路径

define("VIEW_PATH", ROOT_PATH . DS . APP_NAME . DS . 'view'); //视图库

define("CONF_PATH", ROOT_PATH . DS . 'config'); //配置文件路径

define("VENDOR_PATH", ROOT_PATH . DS . 'vendor'); //第三方拓展路径

define("API", explode('?', $_SERVER['REQUEST_URI'])[0]); //API路径

require  APP_PATH . DS . "bootstrap.php";
//自动加载类库
spl_autoload_register(function ($class) {
    $path = APP_PATH;
    foreach (explode('\\', $class) as $key => $value) {
        $path = $path . DS . $value;
    }
    include $path . '.php';
});
try {
    if (in_array(($_SERVER['REQUEST_METHOD'] ?? ""), ['POST', 'PUT']) && isset($_SERVER['CONTENT_TYPE'])) {
        $content_type = explode(';', $_SERVER['CONTENT_TYPE']);
        if (in_array('application/json', $content_type)) {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
    }
} catch (\Exception $e) {
    echo json_encode(['code' => 500, 'msg' => '参数格式不正确']);
    exit;
}

$route = require(CONF_PATH . DS . 'route.php');

if (!isset($route[API])) {
    header("http/1.1 404 Not Found");
    exit;
}

$route[API] = str_replace("/", "\\", $route[API]);
list($class, $func) = explode('@', $route[API]);
$class = new $class;
$res = $class->$func();
if ($res !== NULL) {
    if (is_array($res) || is_object($res)) {
        header('Content-Type:text/json; charset=UTF-8');
        echo json_encode($res);
    } elseif (is_string($res)) {
        header('Content-Type:text/html; charset=UTF-8');
        echo $res;
    }
}
