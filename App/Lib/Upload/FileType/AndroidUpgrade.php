<?php

namespace App\Lib\Upload\FileType;


/**
 * 安卓热更包处理
 * Class AndroidUpgrade
 * @package App\Lib\Upload\FileType
 */
class AndroidUpgrade extends FileAdapter
{
    protected $mimeType = ['application/zip'];

    protected $subDir = 'upgrade/ios/';

    protected $ext = ['zip'];

    /**
     * @param \FileUpload\File $file
     */
    public function callback($file): void
    {
        $targetPath = dirname($file->getRealPath()) . '/' . $this->getFileName($file->getClientFileName());
        $this->removeDir($targetPath);
        $this->extract($file->getRealPath(), $targetPath);

        parent::callback($file);
    }
}