<?php


namespace App\Lib\Upload\FileType;


interface File
{
    /**
     * 获取文件类型
     * @return array
     */
    public function getMimeType() : array;

    /**
     * 获取文件存放子目录
     * @return string
     */
    public function getSubDir() : string;

    /**
     * 获取文件扩展
     * @return array
     */
    public function getExt() : array;

    /**
     * 文件上传后验证
     * @param \FileUpload\File $file
     * @param string $targetPath 上传目录
     * @return bool
     */
    public function validate($file, $targetPath) : bool;

    /**
     * 文件上传成功回调
     * @param \FileUpload\File $file
     */
    public function callback($file) : void;
}