<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/21
 * Time: 17:37
 */

namespace App\RpcService;


use App\Lib\Ssl\AES;
use App\Utility\JsonHelper;
use EasySwoole\Rpc\AbstractService;

class BaseService extends AbstractService
{
    protected $params = [];

    public function serviceName(): string
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    protected function onRequest(?string $action): ?bool
    {
        if (parent::onRequest($action)) {
            $requestData = $this->request()->toArray();

            $decParams = AES::getInstance()->decrypt($requestData['arg']);
            if ($decParams) {
                $this->params = JsonHelper::decode($decParams);
                return true;
            }

            $this->error('dec error');
            return false;
        }
        $this->error('request error');
        return false;
    }

    /**
     * 成功返回
     * @param array $data
     * @return void
     */
    protected function success(array $data = []){
        $this->response()->setStatus(0);
        $this->response()->setResult($data);
        $this->response()->setMsg('success');
    }

    /**
     * 失败返回
     * @param string $msg
     * @return void
     */
    protected function error(string $msg) {
        $this->response()->setStatus(-1);
        $this->response()->setMsg($msg);
    }
}