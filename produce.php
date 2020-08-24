<?php
return [
    'SERVER_NAME' => "EasySwoole",
    'MAIN_SERVER' => [
        'LISTEN_ADDRESS' => '0.0.0.0',
        'PORT' => 9006,
        'SERVER_TYPE' => EASYSWOOLE_WEB_SERVER, //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER,EASYSWOOLE_REDIS_SERVER
        'SOCK_TYPE' => SWOOLE_TCP,
        'RUN_MODEL' => SWOOLE_PROCESS,
        'SETTING' => [
            'worker_num' => 2,
            'reload_async' => true,
            'max_wait_time' => 3,
            'package_max_length' => 52428800,   //单位K,数据包最大50M
        ],
        'TASK' => [
            'workerNum' => 1,
            'maxRunningNum' => 128,
            'timeout' => 15
        ]
    ],
    'TEMP_DIR' => null,
    'LOG_DIR' => null,
    'MYSQL' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'user' => 'root',
        'password' => 'eca4ea514d612f8c',
        'database' => 'dj',
        'timeout' => 5,
        'charset' => 'utf8mb4',
        'maxObjectNum' => 3,
        'minObjectNum' => 1
    ],
    'REDIS' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'auth' => '',
        'maxObjectNum' => 3,
        'minObjectNum' => 1
    ],
    'RPC_SERVER' => [
        'ip' => '0.0.0.0',
        'port' => 20002,
        'secretKey' => '25l3k4j0987fl$@a',
    ],
    'SENTRY' => [
        'dsn' => 'http://6ac48f6ba2f8483da762d28c00fb0366@192.168.1.200:9000/1'
    ],
    'UPLOAD' => [
        'host' => 'http://159.138.145.44:7014/',  //资源地址
        'max_size' => '100M', //文件最大上传大小
        'download_path' => 'file/',   //下载路径
        'upload_path' => '/Public/file/', //上传路径
        'scale' => [//缩略图大小
            'width' => 200,
            'height' => 200,
            'thumb_dir' => 'thumb/'
        ],
    ],

    /*################ 短信服务-云片配置 ##################*/
    'SMS_YUN_PIAN' => [
        "api_key" => "f1de99de721f6a58c4894d6094094e36",
        "real_send" => true,
        "rules" => [
            [
                "cache_sms" => "sms_minute",
                "ttl" => 60,
                "count" => 1,
                'msg' => '一分钟限制一次'
            ],
            [
                "cache_sms" => "sms_hour",
                "ttl" => 3600,
                "count" => 10,
                'msg' => '一小时限制十次'
            ],
            [
                "cache_sms" => "sms_day",
                "ttl" => 86400,
                "count" => 15,
                'msg' => '一天限制十五次'
            ]],
        "tpl_verify_code" => "【蓝川资讯】验证码是#code#。10分钟有效。如非本人操作，请忽略本短信",
        "tpl_agency_apply" => "【蓝川资讯】您的申请已通过，请登录APP进行查看",
        "tpl_balance" => "【蓝川资讯】您当月收益已审核通过，请登录APP进行查看"
    ]
];