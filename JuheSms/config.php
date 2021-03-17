<?php
return [
    'name' => '聚合短信',
    'desc' => '聚合短信',
    'version' => '1.0.3',
    'depend' => '',
    'status' => '0',
    'cate' => '短信',
    'unique' => 'sms',

    'params' => [

        [
            'name' => 'app_key',
            'key' => 'app_key',
            'type' => 'string',
            'value' => '',
            'desc' => 'AppKey',
        ],

        [
            'name' => '验证码模板ID',
            'key' => 'verify_template_id',
            'type' => 'string',
            'value' => '',
            'desc' => '验证码模板ID',
        ],

        [
            'name' => '短信签名',
            'key' => 'sign_name',
            'type' => 'string',
            'value' => '',
            'desc' => '短信签名',
        ],
    ],

    'update_log' => [
        '1.0.3' => [
            'fix 结果回填到表',
        ],
        '1.0.2' => [
            '配置文件更新',
        ],
        '1.0.1' => [
            '聚合短信, 需要compser require overtrue/easy-sms',
        ],
    ],
];
