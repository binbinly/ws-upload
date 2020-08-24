<?php

namespace App\Lib\Upload\Format;

interface Input
{
    /**
     * 上传文件对象格式化为fileUpload所需要的格式
     * @param array $files
     * @return array
     */
    public function formatData(array $files) : array;
}