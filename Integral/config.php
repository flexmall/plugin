<?php
return [
    'name' => '积分',
    'desc' => '积分功能',
    'version' => '1.0.6',
    'depend' => '',
    'status' => '0',
    'cate' => '其他',
    
    'params' => [
         
        [
            'name' => '积分赠送比例',
            'key' => 'give_rate',
            'type' => 'string',
            'value' => '1',
            'desc' => '每支付1块钱赠送N积分',
        ],
         
        [
            'name' => '积分抵扣比例',
            'key' => 'use_rate',
            'type' => 'string',
            'value' => '0.1',
            'desc' => '每1积分可抵扣N元',
        ],
    ],
    
    'update_log' => [
        '1.0.6' => [
             '修改分类',
        ],
        '1.0.5' => [
             '优化事件名',
        ],
        '1.0.4' => [
             '配置文件更新',
        ],
        '1.0.3' => [
             'fix',
        ],
        '1.0.2' => [
             'fix',
        ],
        '1.0.1' => [
             '积分插件',
        ],
    ],
];