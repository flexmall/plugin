<?php
return [
    'name' => '麦讯通短信',
    'desc' => '麦讯通短信',
    'version' => '1.0.3',
    'depend' => '',
    'status' => '1',
    'cate' => '短信',
    'unique' => 'sms',
    
    'params' => [
         
        [
            'name' => '账户',
            'key' => 'account',
            'type' => 'string',
            'value' => '',
            'desc' => '账户',
        ],
         
        [
            'name' => '密码',
            'key' => 'pwd',
            'type' => 'string',
            'value' => '',
            'desc' => '密码',
        ],
         
        [
            'name' => '签名',
            'key' => 'sign',
            'type' => 'string',
            'value' => '',
            'desc' => '签名',
        ],
    ],
    
    'update_log' => [
        '1.0.3' => [
             '签名',
        ],
        '1.0.2' => [
             '签名配置项',
        ],
        '1.0.1' => [
             'v1.0.1',
        ],
    ],
];