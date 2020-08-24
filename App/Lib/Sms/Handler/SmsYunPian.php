<?php


namespace App\Lib\Sms\Handler;


use App\Lib\Sms\SmsAdapter;
use Yunpian\Sdk\YunpianClient;

/**
 * 云片
 * https://www.yunpian.com
 * Class SmsYunPian
 * @package App\Common\Sms\Handler
 */
class SmsYunPian extends SmsAdapter
{
    const TPL_VERIFY = 1;       //验证码模板
    const TPL_AGENCY_APPLY = 2; //推广审核通过模板
    const TPL_BALANCE = 3;      //推广奖励结算审核通过模板

    private $tplList = [
        self::TPL_VERIFY => 'tpl_verify_code',
        self::TPL_AGENCY_APPLY => 'tpl_agency_apply',
        self::TPL_BALANCE => 'tpl_balance'
    ];

    protected $client = null;

    protected function __construct(array $config)
    {
        $this->client = YunpianClient::create($config['api_key']);
        parent::__construct($config);
    }

    /**
     * @param string $mobile
     * @param string $code
     * @param $key
     * @return bool
     */
    public function realSend(string $mobile, string $code, $key = null) : bool
    {
        $param = [YunpianClient::MOBILE => $mobile,
            YunpianClient::TEXT => str_replace('#code#', $code, $this->config[$this->tplList[$key]])];
        $r = $this->client->sms()->single_send($param);

        if ($r->isSucc()) {
            return true;
        }
        $this->err = $r->code() . ':' . $r->exception();
        return false;
    }
}
