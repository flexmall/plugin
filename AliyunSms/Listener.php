<?php

declare(strict_types=1);

namespace App\Plugin\AliyunSms;

use Hyperf\Event\Annotation\Listener as HyperfListener;
use Hyperf\Event\Contract\ListenerInterface;
use App\Listener\PluginBaseListener;
use Overtrue\EasySms\EasySms;

/**
 * @HyperfListener
 */
class Listener extends PluginBaseListener implements ListenerInterface
{

    public function listen(): array
    {
        // 填写要监听的事件
        return [
            \App\Event\Api\SendSms::class
        ];
    }

    public function process(object $event)
    {
        //判断插件是否开启
        if (!$this->status()) {
            return false;
        }

        //获取参数
        $this->param = $event->param;

        //具体业务逻辑
        if ($event instanceof \App\Event\Api\SendSms) {
            $this->send();
        }
    }

    public function send()
    {
        $config = [
            'timeout' => 5.0,
            'default' => [
                'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,
                'gateways' => ['aliyun'],
            ],
            // 可用的网关配置
            'gateways' => [
                'errorlog' => [
                    'file' => BASE_PATH . '/runtime/logs/easy-sms.log',
                ],
                'aliyun' => [
                    'access_key_id' => $this->param('AccessKeyId'),
                    'access_key_secret' => $this->param('AccessKeySecret'),
                    'sign_name' => $this->param('sign_name'),
                ]
            ]
        ];

        //发送短信
        $easySms = new EasySms($config);

        $data = [
            'content' => $this->param['content'],
            'template' => $this->param('verify_template_id'),
            'data' => $this->param['data']
        ];

        try {
            $res = $easySms->send($this->param['phone'], $data);
            var_dump($res);
            $log = [
                'status' => $res['aliyun']['status'],
                'result' => $res['aliyun']['result']['Message']
            ];
        } catch (\Overtrue\EasySms\Exceptions\NoGatewayAvailableException $e) {
            $res = $e->getResults();
            $log = [
                'status' => $res['aliyun']['status'],
                'result' => $res['aliyun']['result']['Message']
            ];
        }

        //记录发送状态
        $log['updated_at'] = date('Y-m-d H:i:s');
        $log['gateway'] = 'aliyun';
        db('sms_log')->where('id', $this->param['id'])->update($log);
    }
}
