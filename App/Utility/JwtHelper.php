<?php


namespace App\Utility;

use Firebase\JWT\JWT;

class JwtHelper
{
    private static $secret = '2sdf&^%asfkf4420fsh^)!@fd';

    private static $sign = 'HS256';

    public static function encode(array $userData, $expire = 7200){
        $now = time();
        $payload = array(
            "iat" => $now,    //发布时间
            "exp" => time() + $expire //到期时间
        );
        $payload = array_merge($payload, $userData);

        /**
         * IMPORTANT:
         * You must specify supported algorithms for your application. See
         * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
         * for a list of spec-compliant algorithms.
         */
        return JWT::encode($payload, self::$secret);
    }

    public static function decode($token){
        return (array) JWT::decode($token, self::$secret, array(self::$sign));
    }
}