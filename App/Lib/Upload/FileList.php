<?php


namespace App\Lib\Upload;

use EasySwoole\Component\Singleton;
use FileUpload\File;

/**
 * 上传文件工具类
 * Class EasySwooleFileHelper
 * @package App\Lib
 */
class FileList extends UploadAdapter
{
    use Singleton;

    /**
     * 获取文件对象
     * @return array
     */
    public function createFiles()
    {
        if (!is_dir($this->uploadPath)) {
            return [];
        }
        return array_values(array_filter(array_map(
            [get_class(), 'createFile'],
            scandir($this->uploadPath)
        )));
    }

    /**
     * 格式化文件对象
     * @param $fileName
     * @return File
     */
    protected function createFile($fileName)
    {
        if ($this->isValidFileObject($fileName)) {
            $file = new File($this->getUploadPath($fileName));
            $file->completed = true;
            return $file;
        }
        return null;
    }

    /**
     * 验证文件对象
     * @param $fileName
     * @return bool
     */
    protected function isValidFileObject($fileName)
    {
        $filePath = $this->getUploadPath($fileName);
        if (strlen($fileName) > 0 && $fileName[0] !== '.' && is_file($filePath)) {
            return true;
        }
        return false;
    }
}