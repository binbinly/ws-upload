<?php

namespace App\Lib\Upload\Format;

interface Output
{
    /**
     * 返回客户端所需数据格式
     * @param array $files
     * @return array
     */
    public function formatData(array $files) : array;
}