<?php

declare(strict_types=1);

namespace App\Plugin\Wxpay;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * @AutoController(prefix="/plugin/wxpay")
 */
class Controller extends AbstractController
{
    //控制器业务逻辑 访问路径:/plugin/wxpay/index
    public function index()
    {
        return 'This is wxpay plugin';
    }

    //回调处理
    public function notify(RequestInterface $request, ResponseInterface $response)
    {
        // 公共配置
        $params = new \Yurun\PaySDK\Weixin\Params\PublicParams;
        $params->appID = plugin_config('Wxpay', 'appid');
        $params->mch_id = plugin_config('Wxpay', 'mch_id');
        $params->key = plugin_config('Wxpay', 'key');

        // SDK实例化，传入公共配置
        $sdk = new \Yurun\PaySDK\Weixin\SDK($params);


        $payNotify = new class extends \Yurun\PaySDK\Weixin\Notify\Pay
        {
            //后续执行操作
            protected function __exec()
            {
                //回调记录
                notify_log('wxpay', $this->data, $this->data['out_trade_no'], $this->data['result_code']);

                // 告诉微信我处理过了，不要再通过了
                \App\Model\Order::paySuccess($this->data['out_trade_no']);
                $this->reply(true, 'OK');
            }
        };
        // 目前主流 Swoole 基本都支持 PSR-7 标准的对象
        // 所以可以直接传入，如何获取请查阅对应框架的文档
        $payNotify->swooleRequest = $request;
        $payNotify->swooleResponse = $response;

        $sdk->notify($payNotify);

        // 处理完成后需要将 $response 从控制器返回或者赋值给上下文
        // 不同框架的操作不同，请自行查阅对应框架的文档
        return 'SUCCESS';
    }
}
