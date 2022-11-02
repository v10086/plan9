<?php
return [
    'mysql' => [
        'default' => [
            'dsn' => 'mysql:host=127.0.0.1;dbname=lumen;charset=utf8mb4;collation=utf8mb4_unicode_ci',
            'user' => 'root',
            'password' => 'zhongbo'
        ],
    ],
    'redis' => [
        'default' => [
            'host'          =>  '127.0.0.1', //地址
            'port'          => '6379', //端口
            'password'      => '', //密码
            'persistent'    => false, //是否长连接  true 是 false 否
            'timeout' => false
        ]
    ]

];
