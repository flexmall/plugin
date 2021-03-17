<?php

declare(strict_types=1);

namespace App\Plugin\Postage;

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
            \App\Event\Api\Settlement::class
        ];
    }

    public function process(object $event)
    {
        //判断插件是否开启
        if (!$this->status()) {
            return false;
        }

        //获取结算全局数据
        $data = data('settlement');

        //用户省份地址code
        $province_code = $data['address']['province_code'];

        foreach ($data['orders'] as $k => $v) {

            $tmp_postage = [];
            foreach ($v['prdts'] as $kk => $vv) {
                //运费规则
                $rule = db('postage_rule')
                    // ->select('first_weight_fee', 'added_weight_fee')
                    ->where('id', $vv['postage_rule_id'])
                    ->cache()
                    ->first();

                //指定地区
                $area = db('postage_area_rule')
                    ->where('postage_id', $vv['postage_rule_id'])
                    ->cache()
                    ->getArray();
                if (is_array($area)) {
                    foreach ($area as $kkk => $vvv) {
                        if (in_array($province_code, explode(',', $vvv['address_code']))) {
                            $rule = $vvv;
                            break;
                        }
                    }
                }

                //运费计算
                $total_weight = floatval($vv['weight']) * $vv['quantity']; //总重量
                $total_weight = ceil($total_weight); //向上取整
                if ($total_weight > 0) {
                    $tmp_postage[] = $rule['first_weight_fee'] + ($total_weight - 1) * $rule['added_weight_fee'];
                }
            }

            //回填到结算数据
            $data['orders'][$k]['postage'] = max($tmp_postage); //订单邮费 取订单最高
            $data['orders'][$k]['realpay'] += max($tmp_postage); //订单金额
            $data['pay']['postage'] += max($tmp_postage); //总邮费
            $data['pay']['realpay'] += max($tmp_postage); //总金额
        }

        //设置处理后的结算数据
        data('settlement', $data);
    }
}
