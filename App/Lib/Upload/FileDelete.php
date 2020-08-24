<?php


namespace App\Lib\Upload;

use EasySwoole\Component\Singleton;

/**
 * 文件删除
 * Class EasySwooleFileHelper
 * @package App\Lib
 */
class FileDelete extends UploadAdapter
{
    use Singleton;

    /**
     * 文件删除
     * @param $filename
     * @return bool
     */
    public function delete($filename){
        $filePath = $this->getUploadPath($filename);
        return is_file($filePath) && unlink($filePath);
    }
}