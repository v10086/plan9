<?php

namespace controller;

class Index extends \controller\Base
{

    public function index()
    {
        $abc = '首页';
        include(VIEW_PATH . '/index.html');
    }

    public function test()
    {
        return [
            'code' => 1,
            'msg' => 'ok'
        ];
    }
}
