<?php
//echo 666;exit;
//初始化mysql连接信息
v10086\DB::$config= config('database.mysql');
//初始化redis连接信息
v10086\Redis::$config= config('database.redis');


//其它 ....

