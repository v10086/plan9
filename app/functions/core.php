<?php

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