<?php
return [
     'name' => '余额支付',
     'desc' => '余额支付',
     'version' => '1.0.11',
     'depend' => '',
     'status' => '1',
     'cate' => '支付',

     'params' => [],

     'update_log' => [
          '1.0.11' => [
               'fix plugin_key',
          ],
          '1.0.10' => [
               'fix',
          ],
          '1.0.9' => [
               '优化插件开关',
          ],
          '1.0.8' => [
               '修改余额支付为amount',
          ],
          '1.0.7' => [
               '修改分类',
          ],
          '1.0.6' => [
               '优化事件名',
          ],
          '1.0.5' => [
               '优化退款',
          ],
          '1.0.4' => [
               '配置文件更新',
          ],
          '1.0.3' => [
               'fix',
          ],
          '1.0.2' => [
               '支付方式改到插件写入',
          ],
          '1.0.1' => [
               '实现余额支付, 同步插件状态到支付方式表',
          ],
     ],
];