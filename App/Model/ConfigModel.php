<?php
namespace App\Model;

use EasySwoole\ORM\AbstractModel;
use EasySwoole\RedisPool\Redis;
use App\Utility\JsonHelper;
use EasySwoole\ORM\DbManager;

Class ConfigModel extends AbstractModel
{
    const CACHE_KEY = "ConfigModelList";    //redis缓存key

    const TYPE_STRING = 'string';
    const TYPE_JSON = 'json';

    protected $tableName = 'system_config';

    /**
     * 配置加载
     * @param null $name
     * @param string $format
     * @param $default
     * @return array|mixed
     * @throws \Exception
     * @throws \Throwable
     */
    public static function init($name = null, $format = self::TYPE_JSON, $default = null)
    {

        $redis = Redis::defer('redis');
        if (!$redis->exists(self::CACHE_KEY)) {
            $list = DbManager::getInstance()->invoke(function ($client) {

                $model = self::invoke($client);
                return $model->field('name, value')->all();
            });
            if ($list) {
                $list = array_combine(array_column($list, 'name'), array_column($list, 'value'));
                $redis->setEx(self::CACHE_KEY, 3600, JsonHelper::encode($list));
            }

        } else {
            $list = JsonHelper::decode($redis->get(self::CACHE_KEY));
        }
        if($name){
            if($format == self::TYPE_JSON) {
                return JsonHelper::decode($list[$name]);
            }
            return $list[$name] ?? $default;
        }
        return $list ?: [];
    }
}