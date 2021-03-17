<?php

declare(strict_types=1);

namespace App\Plugin\Integral;

use App\Listener\PluginBaseListener;
use App\Model\Wallet;
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
            \App\Event\Api\Settlement::class,
            \App\Event\Api\OrderCreate::class,
            \App\Event\Api\OrderClose::class
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


        //结算页计算积分使用
        if ($event instanceof \App\Event\Api\Settlement) {
            $this->calc();
        }

        //创建订单扣减积分
        elseif ($event instanceof \App\Event\Api\OrderCreate) {
            $this->cost();
        }

        //关闭订单退回积分
        elseif ($event instanceof \App\Event\Api\OrderClose) {
            $this->refund();
        }
    }

    //结算页计算积分
    public function calc()
    {
        //获取结算全局数据
        $data = data('settlement');

        //未选择使用积分
        if (!isset($data['param']['use_integral']) || !$data['param']['use_integral']) {
            return;
        }

        //积分不足
        if ($data['wallet']['integral'] <= 0) {
            return;
        }

        //总的
        if ($data['wallet']['integral'] * $this->param('use_rate') > $data['pay']['realpay']) {
            $use_integral = $data['pay']['realpay'] / $this->param('use_rate');
        } else {
            $use_integral = $data['wallet']['integral'];
        }
        $discount = $use_integral * $this->param('use_rate');
        $data['discount']['integral'] = ['use' => $data['wallet']['integral'], 'discount' => $discount];
        $data['pay']['discount'] += $discount;
        $data['pay']['realpay'] -= $discount;

        //平分到每个订单上
        foreach ($data['orders'] as $k => $v) {
            $sub_use_integral = $use_integral * $v['amount'] / $data['pay']['amount'];
            $sub_discount = $discount * $v['amount'] / $data['pay']['amount'];
            $data['orders'][$k]['discount'] += $sub_discount;
            $data['orders'][$k]['realpay'] -= $sub_discount;
            $data['orders'][$k]['discount_arr'][] = ['type' => 'integral', 'amount' => $sub_use_integral, 'discount' => $sub_discount];
        }

        //设置处理后的结算数据
        data('settlement', $data);
    }

    //创建订单扣减积分
    public function cost()
    {
        //判断订单是否使用了积分
        $order = $this->param;
        if (isset($order['discount_arr'])) {
            foreach ($order['discount_arr'] as $kk => $vv) {
                if ($vv['type'] == 'integral') {
                    Wallet::change($order['userid'], -$vv['amount'], 'order_cost', '订单使用积分', $order['id'], 'integral');
                    $discount_log = [
                        'order_id'   => $order['id'],
                        'userid'   => $order['userid'],
                        'type'       => 'integral',
                        'amount'     => $vv['amount'],
                        'discount'   => $vv['discount'],
                        'remark'     => '积分抵扣',
                        'created_at' => date("Y-m-d H:i:s"),
                    ];
                    db('order_discount')->insert($discount_log);
                }
            }
        }
    }

    //取消订单或退款退还积分
    public function refund()
    {
        $order = $this->param;
        $discount = db('order_discount')
            ->where('order_id', $order['id'])
            ->where('status', 'normal')
            ->where('type', 'integral')
            ->first();

        if (!$discount) {
            return;
        }

        Wallet::change($order['userid'], $discount['amount'], 'order_close', '订单关闭积分退回', $order['id'], 'integral');
        db('order_discount')->where('id', $discount['id'])->update(['status' => 'order_close']);
    }
}
