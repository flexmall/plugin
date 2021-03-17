<?php

declare(strict_types=1);

namespace App\Plugin\MicrocomSms;

use Hyperf\Event\Annotation\Listener as HyperfListener;
use Hyperf\Event\Contract\ListenerInterface;
use App\Listener\PluginBaseListener;

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
        $account = $this->param('account');
        $pwd = $this->param('pwd');
        $ts = date('YmdHis');
        $pswd = md5($account . $pwd . $ts);
        $default = [
            'account' => $account,
            'ts' => $ts,
            'pswd' => $pswd,
            'mobile' => $this->param['phone'],
            'msg' => '【' . $this->param('sign') . '】' . $this->param['content'],
            'resptype' => 'json',
            'needstatus' => 'true',
        ];

        $url = "https://www.weiwebs.cn/msg/HttpSendSM";

        $res = http($url, "POST", $default);

        //记录发送状态
        $log['status'] = $res['result'] == 0 ? '成功' : '失败';
        $log['result'] = json_encode($res);
        $log['updated_at'] = date('Y-m-d H:i:s');
        $log['gateway'] = 'microcom';
        db('sms_log')->where('id', $this->param['id'])->update($log);
    }
}
