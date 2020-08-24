<?php


namespace App\Lib\Upload\FileType;

use EasySwoole\EasySwoole\Config;

/**
 * 上传文件类型工厂
 * Class FileFactory
 * @package App\Lib\Upload\FileType
 */
class FileFactory
{
    /**
     * @param int $type
     * @return File
     */
    public static function build(int $type){
        switch ($type) {
            case 0:
                return (new Image())->setScaleConf(Config::getInstance()->getConf('UPLOAD')['scale']);
            case 1:
                return new Android();
            case 2:
                return new Ios();
            case 3:
                return new AndroidUpgrade();
            case 4:
                return new IosUpgrade();
            default:
                return null;
        }
    }
}