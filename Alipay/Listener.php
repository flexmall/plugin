<?php

declare(strict_types=1);

namespace App\Plugin\Alipay;

use App\Listener\PluginBaseListener;
use Hyperf\Event\Annotation\Listener as HyperfListener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @HyperfListener
 */
class Listener extends PluginBaseListener implements ListenerInterface
{
    public function listen(): array
    {
        // 填写要监听的事件
        return [
            \App\Event\Api\OrderPay::class,  // 支付事件
            \App\Event\Api\OrderRefund::class,  // 退款事件
            \App\Event\Admin\PluginChangeStatus::class,  // 开关插件
        ];
    }

    public function process(object $event)
    {
        // 同步插件状态到支付配置表
        if ($event instanceof \App\Event\Admin\PluginChangeStatus) {
            $this->changeStatus($event->param);
        }

        //判断插件是否开启
        if (!$this->status()) {
            return false;
        }

        // 处理支付
        if ($event instanceof \App\Event\Api\OrderPay) {

            $this->pay($event->param);
        }

        //处理退款
        if ($event instanceof \App\Event\Api\OrderRefund) {
            $this->refund($event->param);
        }
    }

    // 支付
    public function pay($id)
    {
        $data = db('pay')->where('id', $id)->first();

        if (!$data) {
            return false;
        }

        //判断支付方式是否为当前支付方式
        if ($data['payway'] != 'alipay') {
            return false;
        }

        //判断订单状态
        if ($data['status'] != 'unpay') {
            return false;
        }

        // 公共配置
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        $params->appID = $this->param('appid');
        $params->appPrivateKey = $this->param('private_key');

        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);

        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\App\Params\Pay\Request;
        $request->notify_url = domain() . '/plugin/alipay/notify';
        $request->return_url = domain();
        $request->businessParams->out_trade_no = $data['flow_id'];
        $request->businessParams->total_amount = $data['amount'];
        $request->businessParams->subject = $data['name'];

        // 跳转到支付页面
        // $pay->redirectExecute($request);

        // 获取支付信息
        try {
            $pay->prepareExecute($request, $url, $data);
            db('pay')->where('id', $id)->update(['pay_info' => json_encode($data)]);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            msg_response('支付失败,请稍后再试' . $res);
        }
        data('payinfo', ['url' => $url, 'info' => http_build_query($data)]);
        return true;
    }

    // 同步插件状态到支付配置表
    public function changeStatus($param)
    {
        if ($param['key'] != 'Alipay') {
            return;
        }

        db('payway')->where('key', 'alipay')->update(['status' => $param['status']]);
    }

    //退款
    public function refund($refund_data)
    {
        //判断支付方式是否为当前支付方式
        if ($refund_data['payway'] != 'alipay') {
            return false;
        }
        // 公共配置
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        $params->appID = $this->param('appid');
        $params->appPrivateKey = $this->param('private_key');

        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);
        // 支付接口
        $request = new \Yurun\PaySDK\AlipayApp\Params\Refund\Request;
        $request->businessParams->out_trade_no = $refund_data['pay']['flow_id'];
        // 需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
        $request->businessParams->refund_amount = $refund_data['refund_amount'];
        $request->businessParams->refund_reason = '订单退款';
        //标识多次退款
        $request->businessParams->out_request_no = 'HZ01RF001';
        // 调用接口
        $result = $pay->execute($request);
        // var_dump('error:', $pay->getError(), 'error_code:', $pay->getErrorCode());
        if ($pay->getErrorCode() != "") {
            msg_response('退款失败' . $pay->getError());
        }
        return true;
    }
}
