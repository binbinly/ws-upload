<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21
 * Time: 15:47
 */

namespace App\RpcService;

use App\Lib\Sms\Handler\SmsYunPian;
use EasySwoole\EasySwoole\Config;
use EasySwoole\RedisPool\Redis;

/**
 * 第三方短信服务
 * Class SmsService
 * @package App\RpcService
 */
class SmsService extends BaseService
{
    /**
     * 发送验证码
     */
    public function send(){
        if (!isset($this->params['mobile'])) {
            $this->error('mobile empty');
            return;
        }
        $handler = SmsYunPian::getInstance(Config::getInstance()->getConf('SMS_YUN_PIAN'));
        $handler->setCallback([SmsService::class, 'callback']);

        $ret = $handler->send($this->params['mobile'], $this->params['key']);
        if($ret === false) {
            $this->error($handler->getErr());
        } else {
            $this->success(['code' => $ret]);
        }
    }

    /**
     * 验证码发送成功回调
     * @param string $mobile 手机号
     * @param string $code 发送的验证码
     */
    public static function callback(string $mobile, string $code)
    {
        $redis = Redis::defer('redis');
        $redis->setEx("sms_code:".$mobile, 600, $code);
    }
}