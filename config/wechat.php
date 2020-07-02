<?php

return [

    //配置获取类（必须实现\acme\contracts\ConnectorContract）
    'class'     => '',

    //是否启用缓存
    'cache'     => true,

    //需要加入think服务的接口shi
    'services'  => [

        //企业微信
        'work' => [

        ],
        //微信支付
        'payment' => [

        ],
        //企业开放平台
        'open_work' => [

        ],
        //小程序
        'mini_program' => [
            'app_code',             // 小程序码
            'customer_service',     // 客服消息
            'data_cube',            // 数据统计与分析
            'auth',                 // 微信登录
            'subscribe_message',    // 订阅消息
        ],
        //开发平台
        'open_platform' => [

        ],
        //小微商户
        'micro_merchant' => [
            'withdraw',             //提现
            'merchantConfig',       //商户配置
        ],
        //公众号
        'official_account' => [
            'base',                  //基础接口
            'server',                //服务端
            'broadcasting',          //消息群发
        ],

    ]

];
