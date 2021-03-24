<?php

declare(strict_types=1);

namespace App\Plugin\AliossUpload;

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
        if ($cache = cache('alioss_token')) {
            data('upload_info', $cache);
            return $cache;
        }

        $id = $this->param('AccessKeyId');
        $key = $this->param('AccessKeySecret');
        $host = 'https://' . trim(trim($this->param('upload_url'), 'http://'), 'https://');
        $callbackUrl = '';
        $dir = $this->param('dir') . '/' . date('Ym');

        $callback_param = array(
            'callbackUrl' => $callbackUrl,
            'callbackBody' => 'filename=${object}&size=${size}&mimeType=${mimeType}&height=${imageInfo.height}&width=${imageInfo.width}',
            'callbackBodyType' => "application/x-www-form-urlencoded"
        );
        $callback_string = json_encode($callback_param);

        $base64_callback_body = base64_encode($callback_string);
        $now = time();
        $expire = 300;
        $end = $now + $expire;
        $expiration = $this->gmt_iso8601($end);


        //最大文件大小.用户可以自己设置
        $condition = array(0 => 'content-length-range', 1 => 0, 2 => 1048576000);
        $conditions[] = $condition;

        // 表示用户上传的数据，必须是以$dir开始，不然上传会失败，这一步不是必须项，只是为了安全起见，防止用户通过policy上传到别人的目录。
        $start = array(0 => 'starts-with', 1 => '$key', 2 => $dir);
        $conditions[] = $start;


        $arr = array('expiration' => $expiration, 'conditions' => $conditions);
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $key, true));

        $response = array();
        $response['accessid'] = $id;
        $response['uploadurl'] = $host;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['dir'] = $dir;

        $response['type'] = 'alioss';
        $response['domain'] = 'https://' . $this->param('domain') . '/';
        cache('alioss_token', $response, 240);
        data('upload_info', $response);
        return $response;
    }

    public function gmt_iso8601($time)
    {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration . "Z";
    }
}
