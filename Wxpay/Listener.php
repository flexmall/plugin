<?php

declare(strict_types=1);

namespace App\Plugin\Wxpay;

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
            \App\Event\Api\OrderPay::class,
            \App\Event\Api\OrderRefund::class,
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
        if ($data['payway'] != 'wxpay') {
            return false;
        }

        //判断订单状态
        if ($data['status'] != 'unpay') {
            return false;
        }

        // 公共配置
        $params = new \Yurun\PaySDK\Weixin\Params\PublicParams;
        $params->appID  = $this->param('appid');
        $params->mch_id = $this->param('mch_id');
        $params->key    = $this->param('key');
        $params->certPath = BASE_PATH . $this->param('cert_path');
        $params->keyPath  = BASE_PATH . $this->param('key_path');
        // $params->sub_appid= $this->param('sub_appid'); 
        // $params->sub_mch_id= $this->param('sub_mch_id');

        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($params);

        // 支付接口
        $request = new \Yurun\PaySDK\Weixin\APP\Params\Pay\Request;
        $request->body             = $data['name'];        // 商品描述
        $request->out_trade_no     = $data['flow_id'];     // 订单号
        $request->total_fee        = $data['amount'] * 100; // 订单总金额，单位为：分
        $request->spbill_create_ip = get_ip();            //用户ip  
        $request->notify_url       = domain() . '/plugin/wxpay/notify'; //异步通知地址

        // 获取支付信息
        try {
            $result = $pay->execute($request);
            db('pay')->where('id', $id)->update(['pay_info' => json_encode($result)]);
        } catch (\Exception $e) {
            $res = $e->getMessage();
            msg_response('支付失败,请稍后再试' . $res);
        }

        if ($pay->checkResult()) {
            $clientRequest = new \Yurun\PaySDK\Weixin\APP\Params\Client\Request;
            $clientRequest->prepayid = $result['prepay_id'];
            try {
                $pay->prepareExecute($clientRequest, $url, $data);
                db('pay')->where('id', $id)->update(['pay_info' => json_encode($data)]);
            } catch (\Exception $e) {
                $res = $e->getMessage();
                msg_response('支付失败,请稍后再试' . $res);
            }
        } else {
            msg_response($pay->getError(), $pay->getErrorCode());
        }
        data('payinfo', ['url' => $url, 'info' => $data]);
        return true;
    }

    //退款
    public function refund($refund_data)
    {
        //判断支付方式是否为当前支付方式
        if ($refund_data['payway'] != 'wxpay') {
            return false;
        }

        // 公共配置
        $params = new \Yurun\PaySDK\Weixin\Params\PublicParams;
        $params->appID  = $this->param('appid');
        $params->mch_id = $this->param('mch_id');
        $params->key    = $this->param('key');
        $params->certPath = BASE_PATH . $this->param('cert_path');
        $params->keyPath  = BASE_PATH . $this->param('key_path');
        // SDK实例化，传入公共配置
        $pay = new \Yurun\PaySDK\Weixin\SDK($params);
        $request = new \Yurun\PaySDK\Weixin\Refund\Request;
        // 商户订单号
        $request->out_trade_no = $refund_data['pay']['flow_id'];
        $request->out_refund_no  = $refund_data['refund_flow_id']; // 商户退款单号
        $request->total_fee      = $refund_data['pay']['amount'] * 100; // 订单总金额，单位为分，只能为整数
        $request->refund_fee     = $refund_data['refund_amount'] * 100; // 退款总金额，订单总金额，单位为分，只能为整数
        $result = $pay->execute($request);
        var_dump('error:', $pay->getError(), 'error_code:', $pay->getErrorCode());
        if ($pay->getErrorCode() != "") {
            msg_response('退款失败' . $pay->getError());
        }
        return true;
    }

    // 同步插件状态到支付配置表
    public function changeStatus($param)
    {
        if ($param['key'] != 'Wxpay') {
            return;
        }

        db('payway')->where('key', 'wxpay')->update(['status' => $param['status']]);
    }
}
