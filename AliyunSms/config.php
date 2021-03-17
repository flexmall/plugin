<?php
return [
    'name' => '阿里云短信',
    'desc' => '阿里云短信',
    'version' => '1.0.1',
    'depend' => '',
    'status' => '0',
    'cate' => '短信',
    'unique' => 'sms',

    'params' => [

        [
            'name' => 'AccessKeyId',
            'key' => 'AccessKeyId',
            'type' => 'string',
            'value' => '',
            'desc' => '阿里云AccessKeyId',
        ],

        [
            'name' => 'AccessKeySecret',
            'key' => 'AccessKeySecret',
            'type' => 'string',
            'value' => '',
            'desc' => '阿里云AccessKeySecret',
        ],

        [
            'name' => '短信签名',
            'key' => 'sign_name',
            'type' => 'string',
            'value' => '',
            'desc' => '短信签名',
        ],

        [
            'name' => '验证码模板ID',
            'key' => 'verify_template_id',
            'type' => 'string',
            'value' => '',
            'desc' => '验证码模板ID',
        ],
    ],

    'update_log' => [
        '1.0.1' => [
            '阿里云短信, 需要composer require overtrue/easy-sms',
        ],
    ],
];
