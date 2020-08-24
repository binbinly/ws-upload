<?php


namespace App\Lib\Sms;


use EasySwoole\Component\Singleton;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Utility\Random;

abstract class SmsAdapter implements SmsInterface
{
    use Singleton;

    protected $config;

    protected $callback;

    protected $err;

    protected $rules = [];

    //是否真实发送验证码
    protected $isReal = 1;

    protected function __construct(array $config)
    {
        $this->config = $config;

        isset($config['real_send']) && $this->isReal = $this->config['real_send'];
        isset($config['rules']) && $this->setRules($this->config['rules']);
    }

    /**
     * @return mixed
     */
    public function getErr()
    {
        return $this->err;
    }

    /**
     * 生成验证码
     * @param int $length
     * @return bool|string
     */
    public function createCode($length = 4){
        return Random::number($length);
    }

    /**
     * 绑定回调
     * @param $callback
     */
    public function setCallback($callback): void
    {
        $this->callback = $callback;
    }

    /**
     * 设置规则
     * @param $rules
     */
    public function setRules($rules){
        $this->rules = $rules;
    }

    /**
     * 发送验证码
     * @param string $mobile
     * @param string $key
     * @return bool | string
     */
    public function send(string $mobile, $key)
    {
        $ret = true;
        $code = $this->createCode();
        if($this->isReal) {
            if(!$this->checkRules($mobile)) {
                return false;
            }
            $ret = $this->realSend($mobile, $code, $key);

            $ret && $this->execRules($mobile);
        }
        $ret && call_user_func($this->callback, $mobile, $code);
        return $ret ? $code : false;
    }

    /**
     * 发送成功执行规则
     * @param $mobile
     */
    protected function execRules($mobile){
        if(!$this->rules) return ;
        $redis = Redis::defer('redis');
        foreach($this->rules as $rule) {
            $redis->incr($rule['cache_sms'].$mobile);
            $redis->expire($rule['cache_sms'].$mobile, $rule['ttl']);
        }
    }

    /**
     * 验证规则
     * @param $mobile
     * @return bool
     */
    protected function checkRules($mobile) {
        if(!$this->rules) return true;

        $redis = Redis::defer('redis');
        foreach($this->rules as $rule) {
            if($redis->get($rule['cache_sms'].$mobile) >= $rule['count']) {
                $this->err = $rule['msg'];
                return false;
            }
        }
        return true;
    }
}
