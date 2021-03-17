<?php

declare(strict_types=1);

namespace App\Plugin\Alipay;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * @AutoController(prefix="/plugin/alipay")
 */
class Controller extends AbstractController
{
    //控制器业务逻辑 访问路径:/plugin/Alipay/index
    public function index()
    {
        return 'This is Alipay plugin';
    }

    //回调处理
    public function notify()
    {
        $params = new \Yurun\PaySDK\AlipayApp\Params\PublicParams;
        $params->appPublicKey = plugin_config('Alipay', 'public_key');
        $params->appPrivateKey = plugin_config('Alipay', 'private_key');;
        $pay = new \Yurun\PaySDK\AlipayApp\SDK($params);

        $data = post();

        //回调记录
        notify_log('alipay', $data, $data['out_trade_no']);

        //回调支付成功
        if ($pay->verifyCallback($data)) {
            \App\Model\Order::paySuccess($data['out_trade_no']);
        }

        return 'success';
    }
}
