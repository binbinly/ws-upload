<?php
namespace EasySwoole\EasySwoole;


use App\RpcService\SmsService;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Message\Status;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\ORM\Db\Connection;
use EasySwoole\ORM\DbManager;
use EasySwoole\RedisPool\Redis;
use EasySwoole\Redis\Config\RedisConfig;
use EasySwoole\ORM\Db\Config as DbConfig;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\Rpc\Rpc;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        // TODO: Implement mainServerCreate() method.

        self::redisAndRpcInit();
        //self::mysqlInit();

        \Sentry\init(Config::getInstance()->getConf('SENTRY'));
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        //跨域设置
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $response->withHeader('Access-Control-Allow-Credentials', 'true');
        $response->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With， Content-Length');
        if ($request->getMethod() === 'OPTIONS') {
            $response->withStatus(Status::CODE_OK);
            return false;
        }
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }

    /**
     * redis连接池注册
     * rpc服务注册
     * @throws \EasySwoole\Pool\Exception\Exception
     * @throws \EasySwoole\RedisPool\RedisPoolException
     * @throws \EasySwoole\RedisPool\Exception\Exception
     */
    private static function redisAndRpcInit(){

        $redisConfig = Config::getInstance()->getConf("REDIS");
        $config = new RedisConfig($redisConfig);
        $redisPoolConfig = Redis::getInstance()->register('redis', $config);
        //配置连接池连接数
        $redisPoolConfig->setMinObjectNum($redisConfig['minObjectNum']);
        $redisPoolConfig->setMaxObjectNum($redisConfig['maxObjectNum']);

        //rpc注册
        $rpcConfig = config::getInstance()->getConf("RPC_SERVER");
        $manager = new RedisManager(new RedisPool($config));
        //配置Rpc实例
        $rpcConfigObj = new \EasySwoole\Rpc\Config();
        //这边用于指定当前服务节点ip，如果不指定，则默认用UDP广播得到的地址
        $rpcConfigObj->setServerIp($rpcConfig['ip']);
        $rpcConfigObj->setListenPort($rpcConfig['port']);
        $rpcConfigObj->setNodeManager($manager);
        /*
         * 配置初始化
         */
        Rpc::getInstance($rpcConfigObj);
        //添加服务
        Rpc::getInstance()->add(new SmsService());
        Rpc::getInstance()->attachToServer(ServerManager::getInstance()->getSwooleServer());
    }

    /**
     * mysql orm注册
     * @throws \EasySwoole\Pool\Exception\Exception
     */
    private static function mysqlInit(){

        $mysqlConfig = Config::getInstance()->getConf("MYSQL");
        $config = new DbConfig($mysqlConfig);

        DbManager::getInstance()->addConnection(new Connection($config));
    }
}