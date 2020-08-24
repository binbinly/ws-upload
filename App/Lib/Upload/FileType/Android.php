<?php

namespace App\Lib\Upload\FileType;


/**
 * 安卓包处理
 * Class Android
 * @package App\Lib\Upload\FileType
 */
class Android extends FileAdapter
{
    protected $mimeType = ['application/zip'];

    protected $subDir = 'android/';

    protected $ext = ['apk'];
}