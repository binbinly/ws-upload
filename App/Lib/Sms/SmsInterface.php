<?php


namespace App\Lib\Sms;


interface SmsInterface
{
    /**
     * 真实发送验证码
     * @param string $mobile 发送的手机号
     * @param string $code 验证码
     * @param $key
     * @return bool 成功/失败
     */
    public function realSend(string $mobile, string $code, $key = null) : bool;
}
