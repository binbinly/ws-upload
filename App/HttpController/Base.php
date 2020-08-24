<?php


namespace App\HttpController;


use App\Utility\JwtHelper;
use EasySwoole\Http\AbstractInterface\Controller;

class Base extends Controller
{
    protected $userData;

    function index()
    {
        // TODO: Implement index() method.
    }

    protected function onRequest(?string $action): ?bool
    {
        $token = $this->request()->getQueryParam('token');
        if (!$token){
            $this->error(202, '非法操作');
            return false;
        }
        $userData = JwtHelper::decode($token);
        if (is_array($userData) && isset($userData['cli'])) {
            $this->userData = $userData;
            return true;
        }else{
            //这一步可以给前端响应数据，告知前端未登录
            $this->error(202, '非法操作');
            //返回false表示不继续往下执行控制器action
            return  false;
        }
    }

    /**
     * 非法路由时响应
     * @param string|null $action
     */
    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $this->writeJson(404, null, 'not found');
    }

    /**
     * 异常捕获
     * @param \Throwable $throwable
     */
    function onException(\Throwable $throwable): void
    {
        $this->error(503, $throwable->getMessage());
    }

    /**
     * 成功返回
     * @param $result
     * @return bool
     */
    protected function success($result){
        return $this->writeJson(200, $result, 'success');
    }

    /**
     * 异常返回
     * @param int $code
     * @param string $msg
     * @return bool
     */
    protected function error($code = 500, $msg = 'error'){
        return $this->writeJson($code, null, $msg);
    }

    protected function writeJson($statusCode = 200, $result = null, $msg = null)
    {
        if (!$this->response()->isEndResponse()) {
            $data = Array(
                "code" => $statusCode,
                "files" => $result,
                "msg" => $msg
            );
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 自定义json格式返回
     * @param int $statusCode
     * @param null $data
     * @return bool
     */
    protected function writeCustomJson($statusCode = 200, $data = null)
    {
        if (!$this->response()->isEndResponse()) {
            $this->response()->write(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            $this->response()->withHeader('Content-type', 'application/json;charset=utf-8');
            $this->response()->withStatus($statusCode);
            return true;
        } else {
            return false;
        }
    }
}