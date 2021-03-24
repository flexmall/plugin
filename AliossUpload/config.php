<?php
return [
    'name' => '阿里云上传',
    'desc' => '阿里云上传插件,上传的文件统一保存到阿里云',
    'version' => '1.0.0',
    'depend' => '',
    'status' => '1',
    'cate' => '上传',
    'unique' => 'upload',
    
    'params' => [
         
        [
            'name' => 'AccessKeyId',
            'key' => 'AccessKeyId',
            'type' => 'string',
            'value' => '',
            'desc' => 'AccessKeyId',
        ],
         
        [
            'name' => 'AccessKeySecret',
            'key' => 'AccessKeySecret',
            'type' => 'string',
            'value' => '',
            'desc' => 'AccessKeySecret',
        ],
         
        [
            'name' => '上传地址',
            'key' => 'upload_url',
            'type' => 'string',
            'value' => '',
            'desc' => '在阿里云控制台创建Bucket后复制 如: xxx.oss-cn-guangzhou.aliyuncs.com',
        ],
         
        [
            'name' => '自定义域名',
            'key' => 'domain',
            'type' => 'string',
            'value' => '',
            'desc' => '上传后资源的访问域名, 需要先在控制台绑定',
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