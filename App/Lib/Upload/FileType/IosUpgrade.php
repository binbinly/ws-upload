<?php

namespace App\Lib\Upload\FileType;


/**
 * ios热更包处理
 * Class IosUpgrade
 * @package App\Lib\Upload\FileType
 */
class IosUpgrade extends FileAdapter
{
    protected $mimeType = ['application/zip'];

    protected $subDir = 'upgrade/android/';

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