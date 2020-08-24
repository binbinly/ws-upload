<?php


namespace App\Lib\Upload;

use EasySwoole\Component\Singleton;
use FileUpload\File;

/**
 * 文件下载
 * Class EasySwooleFileHelper
 * @package App\Lib
 */
class FileDownload extends UploadAdapter
{
    use Singleton;

    //分片大小
    const CHUNK_SIZE = 2 * 1024 * 1024; //10M

    public function getHeader($fileName){
        $filePath = $this->getUploadPath($fileName);
        if (!is_file($filePath)) {
            return [false, false];
        }
        $file = new File($filePath);

        //则 script 和 styleSheet 元素会拒绝包含错误的 MIME 类型的响应。这是一种安全功能，有助于防止基于 MIME 类型混淆的攻击
        $header = [
            'X-Content-Type-Options' => 'nosniff',
            'Content-Length' => $file->getSize(),
            'Last-Modified' => gmdate('D, d M Y H:i:s T', filemtime($file->getRealPath()))
        ];
        if (!$file->isImage()) {
            $header['Content-Type'] = 'application/octet-stream';
            $header['Accept-Ranges'] = 'bytes';
            $header['Content-Disposition'] = 'attachment; filename="'.$fileName.'"';
        } else {
            $header['Content-Type'] = $file->getType();
            $header['Content-Disposition'] = 'inline; filename="'.$fileName.'"';
        }
        return [$file, $header];
    }
}