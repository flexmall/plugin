<?php
return [
    'name' => '官方微信支付',
    'desc' => '官方微信支付',
    'version' => '1.0.6',
    'depend' => '',
    'status' => '1',
    'cate' => '支付',

    'params' => [

        [
            'name' => '应用appid',
            'key' => 'appid',
            'type' => 'string',
            'value' => '',
            'desc' => '应用appid',
        ],

        [
            'name' => '商户号mch_id',
            'key' => 'mch_id',
            'type' => 'string',
            'value' => '',
            'desc' => '商户号mch_id',
        ],

        [
            'name' => 'API密钥',
            'key' => 'key',
            'type' => 'string',
            'value' => '',
            'desc' => 'API密钥',
        ],

        [
            'name' => '证书地址',
            'key' => 'cert_path',
            'type' => 'string',
            'value' => '',
            'desc' => '证书地址cert_path',
        ],

        [
            'name' => '证书私钥地址',
            'key' => 'key_path',
            'type' => 'string',
            'value' => '',
            'desc' => '证书私钥地址key_path',
        ],
    ],

    'update_log' => [
        '1.0.6' => [
            '优化插件开关',
        ],
        '1.0.5' => [
            '支付错误处理',
        ],
        '1.0.4' => [
            '优化事件名',
        ],
        '1.0.3' => [
            '回调优化',
        ],
        '1.0.2' => [
            'add:回调记录',
        ],
        '1.0.1' => [
            '官方微信支付',
        ],
    ],
];
