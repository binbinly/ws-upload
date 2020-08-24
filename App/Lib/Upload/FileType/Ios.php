<?php

namespace App\Lib\Upload\FileType;


/**
 * IOS包处理
 * Class Ios
 * @package App\Lib\Upload\FileType
 */
class Ios extends FileAdapter
{
    protected $mimeType = ['application/zip', 'text/xml'];

    protected $subDir = 'ios/';

    protected $ext = ['ipa', 'plist'];
}