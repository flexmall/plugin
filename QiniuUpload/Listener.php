<?php

declare(strict_types=1);

namespace App\Plugin\QiniuUpload;

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
            \App\Event\Admin\Upload::class
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
        if ($event instanceof \App\Event\Admin\Upload) {
            $this->index();
        }
    }

    public function index()
    {
        $exists = data('upload_info');

        //已有不处理
        if ($exists) {
            return false;
        }

        //获取配置
        if ($cache = cache('qiniu_token')) {
            data('upload_info', $cache);
            return $cache;
        }

        // 用于签名的公钥和私钥
        $accessKey = $this->param('access_key');
        $secretKey = $this->param('secret_key');
        $bucket = $this->param('bucket');
        $upload_url = $this->param('upload_url');
        $domain = $this->param('domain');

        if (!$accessKey || !$secretKey || !$bucket || !$upload_url || !$domain) {
            msg_response('请在七牛插件中配置上传参数');
        }
        // 初始化签权对象
        $auth = new \Qiniu\Auth($accessKey, $secretKey);
        // 生成上传Token
        $data = [
            'token'    => $auth->uploadToken($bucket),
            'bucket'   => $bucket,
            'uploadurl' => $upload_url,
            'domain'   => $domain,
            'type'   => 'qiniu',
            'dir' => $this->param('dir') . '/' . date('Ym')
        ];
        //缓存token
        cache('qiniu_token', $data, 60 * 60);
        data('upload_info', $data);
        return $data;
    }
}
