<?php

namespace App\Lib\Upload;

use App\Lib\Upload\FileType\File;
use App\Lib\Upload\Format\Input;

abstract class UploadAdapter
{
    /** @var File */
    protected $fileType;

    protected $conf;

    protected $uploadPath;

    /** @var Input */
    protected $dataFormat;

    public function __construct(array $config)
    {
        $this->conf = $config;
    }

    /**
     * @param Input $dataFormat
     * @@return $this
     */
    public function setDataFormat(Input $dataFormat)
    {
        $this->dataFormat = $dataFormat;
        return $this;
    }

    /**
     * 设置上传类型
     * @param File $type
     * @return $this
     */
    public function setFileType(File $type)
    {
        $this->fileType = $type;
        $this->uploadPath = EASYSWOOLE_ROOT . $this->conf['upload_path'] . $type->getSubDir();
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
        return $this;
    }

    /**
     * 获取下载路径
     * @param $fileName
     * @return string
     */
    protected function getUploadPath($fileName)
    {
        return $this->uploadPath . $fileName;
    }

    /**
     * 获取下载地址
     * @param $fileName
     * @return string
     */
    public function getDownloadUrl($fileName) {
        return $this->conf['host'] .$this->conf['download_path'] . $this->fileType->getSubDir() . $fileName;
    }

    /**
     * 获取图片压缩目录
     * @return mixed
     */
    public function getThumbDir(){
        return $this->fileType->getThumbDir();
    }
}