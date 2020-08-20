<?php

return [
    'wechat' =>  [
        /**
         * 微信配置信息
         * class => 需要实现 \acme\contracts\ConnectorContract
         * array => 根据easywechat官网配置 [https://www.easywechat.com/docs/master/official-account/index]
         */
        'config' =>  [
            //微信支付配置
            'payment'           =>  [],
            //小程序配置
            'miniProgram'       =>  [],
            //微信开发平台第三平台
            'openPlatform'      =>  [],
            //公众号配置
            'officialAccount'   =>  [],
            //企业微信
            'work'              =>  [],
            //企业微信开放平台
            'openWork'          =>  [],
            //小微商户配置
            'microMerchant'     =>  [],
        ]
    ]
];
