<?php


namespace App\Lib\Upload\Format;


use EasySwoole\Http\Message\UploadFile;

/**
 * easyswoole文件对象格式化为jquery-file-upload数据格式
 * Class ES
 * @package App\Lib\Upload\Format
 */
class ESInput implements Input
{

    /**
     * @inheritDoc
     */
    public function formatData(array $files) : array
    {
        $formatFiles = [];
        /** @var UploadFile $file */
        foreach ($files as $i => $file) {
            $formatFiles['name'][$i] = $file->getClientFilename();
            $formatFiles['type'][$i] = $file->getClientMediaType();
            $formatFiles['tmp_name'][$i] = $file->getTempName();
            $formatFiles['error'][$i] = $file->getError();
            $formatFiles['size'][$i] = $file->getSize();
        }
        return $formatFiles;
    }
}