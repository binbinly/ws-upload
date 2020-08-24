<?php

namespace App\Lib\Upload\FileType;

use ZipArchive;

abstract class FileAdapter implements File
{
    //文件类型
    protected $mimeType;

    //文件存放子目录
    protected $subDir;

    //文件扩展
    protected $ext;

    //是否修改文件名 ,默认不修改
    protected $change = false;

    /**
     * @return $this
     */
    public function changeTrue(){
        $this->change = true;
        return $this;
    }

    /**
     * @return array
     */
    public function getMimeType() : array
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getSubDir() : string
    {
        if ($this->change) {
            return $this->subDir . date('Ym') . '/';
        }
        return $this->subDir;
    }

    /**
     * @return array
     */
    public function getExt() : array
    {
        return $this->ext;
    }

    /**
     * @param \FileUpload\File $file
     * @param string $targetPath
     * @return bool
     */
    public function validate($file, $targetPath): bool
    {
        $ext = strtolower(substr($file->getClientFileName(), strrpos($file->getClientFileName(), '.') + 1));
        if (!in_array($ext, $this->ext)) {
            return false;
        }
        return true;
    }

    /**
     * @param \FileUpload\File $file
     */
    public function callback($file): void
    {
        // TODO: Implement callback() method.
    }

    /**
     * 文件是否存在
     * {@inheritdoc}
     */
    public function doesFileExist($path)
    {
        return file_exists($path);
    }

    /**
     * 获取文件名去除后缀
     * @param string $filename 文件名
     * @return false|string
     */
    protected function getFileName($filename){
        return substr($filename, 0, strpos($filename, '.'));
    }

    /**
     * 删除目录
     * @param $dir
     */
    protected function removeDir($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        //先删除文件
        $block_info = scandir($dir, 0);
        // 除去无用文件
        foreach ($block_info as $key => $block) {
            if ($block == '.' || $block == '..') {
                continue;
            }
            if (is_dir($dir . '/' . $block)) {
                $this->removeDir($dir . '/' . $block);
            } else {
                unlink($dir . '/' . $block);
            }
        }
        rmdir($dir);
    }

    /**
     * 解压
     * @param $zipPath
     * @param $targetPath
     */
    protected function extract($zipPath, $targetPath){
        if(!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        $zip = new ZipArchive();
        $openRes = $zip->open($zipPath);
        if ($openRes === TRUE) {
            //$zip->setPassword('1234');
            $zip->extractTo($targetPath);
            $zip->close();
        }
    }
}