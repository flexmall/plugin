<?php
return [
     'name' => '支付宝支付',
     'desc' => '支付宝支付',
     'version' => '1.0.0',
     'depend' => '',
     'status' => '0',
     'cate' => '支付',

     'params' => [

          [
               'name' => 'appid',
               'key' => 'appid',
               'type' => 'string',
               'value' => '',
               'desc' => '支付宝应用appid',
          ],

          [
               'name' => '商户私钥',
               'key' => 'private_key',
               'type' => 'text',
               'value' => '',
               'desc' => '支付宝公钥',
          ],
     ],

     'update_log' => [],
];
