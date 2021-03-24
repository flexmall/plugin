<?php
return [
    'name' => '七牛上传',
    'desc' => '七牛上传插件,上传的文件统一保存到七牛',
    'version' => '1.0.0',
    'depend' => '',
    'status' => '1',
    'cate' => '上传',
    'unique' => 'upload',
    
    'params' => [
         
        [
            'name' => 'access_key',
            'key' => 'access_key',
            'type' => 'string',
            'value' => '',
            'desc' => 'access_key',
        ],
         
        [
            'name' => 'secret_key',
            'key' => 'secret_key',
            'type' => 'string',
            'value' => '',
            'desc' => 'secret_key',
        ],
         
        [
            'name' => '存储空间bucket',
            'key' => 'bucket',
            'type' => 'string',
            'value' => '',
            'desc' => '存储空间bucket',
        ],
         
        [
            'name' => '七牛上传区域地址',
            'key' => 'upload_url',
            'type' => 'string',
            'value' => '',
            'desc' => '七牛上传区域地址 华南: https://upload-z2.qiniup.com',
        ],
         
        [
            'name' => '自定义域名',
            'key' => 'domain',
            'type' => 'string',
            'value' => '',
            'desc' => '上传后资源的访问域名, 需要先在七牛绑定',
        ],
         
        [
            'name' => '上传目录',
            'key' => 'dir',
            'type' => 'string',
            'value' => '',
            'desc' => '上传目录',
        ],
    ],
];