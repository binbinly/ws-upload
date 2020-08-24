<?php


namespace App\Lib\Upload\Format;


use App\Lib\Upload\UploadAdapter;
use FileUpload\File;
use StdClass;

/**
 * 格式化为jquery-file-upload前端组件数据
 * Class JqFileOutput
 * @package App\Lib\Upload\Format
 */
class JqFileOutput implements Output
{
    /** @var UploadAdapter */
    protected $uploadAdapter;

    public function __construct($uploadAdapter)
    {
        $this->uploadAdapter = $uploadAdapter;
    }

    /**
     * 是否支持删除
     * @return bool
     */
    protected function isDelete(){
        return false;
    }

    /**
     * @inheritDoc
     */
    public function formatData(array $files): array
    {
        if (!$files) return [];
        $filesFormat = [];

        /** @var File $file */
        foreach ($files as $file) {
            $std = new StdClass();
            $std->name = $file->getFilename();
            $std->size = $file->getSize();
            $std->type = $file->getType();

            //check if the upload was completed
            if ($file->completed) {//上传成功
                $std->url = $this->uploadAdapter->getDownloadUrl($file->getFilename());
                if ($file->isImage()) {//图片类型压缩
                    $std->thumbnailUrl = $this->uploadAdapter->getDownloadUrl($this->uploadAdapter->getThumbDir() . $file->getFilename());
                }
                if ($this->isDelete()) {
                    $std->deleteUrl = '';
                    $std->deleteType = 'DELETE';
                }
            } else {
                $std->error = $file->error;
            }
            $filesFormat[] = $std;
        }
        return $filesFormat;
    }
}