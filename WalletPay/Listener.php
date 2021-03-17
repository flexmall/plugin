<?php

declare(strict_types=1);

namespace App\Plugin\WalletPay;

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
            \App\Event\Admin\PluginChangeStatus::class,  // 开关插件
            \App\Event\Api\OrderRefund::class,
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
        if ($data['payway'] != 'amount') {
            return false;
        }

        //判断订单状态
        if ($data['status'] != 'unpay') {
            return false;
        }

        //扣减余额
        \App\Model\Wallet::change($data['userid'], -$data['amount'], 'order_pay', '订单支付', $data['id']);

        //支付成功
        $result = \App\Model\Order::paySuccess($data['flow_id']);

        return $result;
    }

    // 同步插件状态到支付配置表
    public function changeStatus($param)
    {
        if ($param['key'] != 'WalletPay') {
            return;
        }

        db('payway')->where('key', 'amount')->update(['status' => $param['status']]);
    }

    //退款
    public function refund($refund_data)
    {
        //判断支付方式是否为当前支付方式
        if ($refund_data['payway'] != 'wallet') {
            return false;
        }
        //退还至余额
        \App\Model\Wallet::change($refund_data['pay']['userid'], $refund_data['refund_amount'], 'order_refund', '订单退款,返还至余额' . $refund_data['order']['flow_id'], $refund_data['order']['id']);
    }
}
