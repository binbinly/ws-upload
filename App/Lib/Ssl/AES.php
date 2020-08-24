<?php


namespace App\Lib\Ssl;


use EasySwoole\Component\Singleton;

class AES
{
    use Singleton;

    protected $method = 'AES-128-CBC';  //加密算法

    protected $secret_key = '25l3k4j0987fl$@a';  //密钥

    /**
     * AES加密算法
     * @param string $content 加密内容
     * @return string
     */
    public function encrypt($content)
    {
        $result = openssl_encrypt($content, $this->method, $this->secret_key, OPENSSL_RAW_DATA, $this->secret_key);
        return base64_encode($result);
    }

    /**
     * AES解密算法
     * @param string $content 密文
     * @return string
     */
    public function decrypt($content)
    {
        $content = base64_decode($content);
        return openssl_decrypt($content, $this->method, $this->secret_key, OPENSSL_RAW_DATA, $this->secret_key);
    }
}
