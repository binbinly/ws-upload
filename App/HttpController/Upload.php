<?php


namespace App\HttpController;


use App\Lib\Upload\FileType\File;
use App\Lib\Upload\FileType\FileFactory;
use App\Lib\Upload\Format\ESInput;
use App\Lib\Upload\Format\JqFileOutput;
use App\Lib\Upload\FileDelete;
use App\Lib\Upload\FileDownload;
use App\Lib\Upload\FileUp;
use EasySwoole\EasySwoole\Config;
use EasySwoole\Http\Request;
use App\Lib\Upload\FileList;

/**
 * 文件上传
 * Class Upload
 * @package App\HttpController
 */
class Upload extends Base
{
    /** @var File */
    protected $fileType;

    public function index() {
        $request =  $this->request();
        $server = $request->getServerParams();
        $type = intval($this->userData['tid'] ?? 0);
        $fileType = FileFactory::build($type);
        if(!$fileType) {
            return $this->error(400, 'type err');
        }
        if (!isset($this->userData['isc']) || !$this->userData['isc']) {
            $fileType->changeTrue();
        }
        $this->fileType = $fileType;

        switch ($server['request_method']) {
            case 'OPTIONS':
            case 'HEAD':
                $this->head($server);
                break;
            case 'GET':
                $this->get();
                break;
            case 'PATCH':
            case 'PUT':
            case 'POST':
                $this->post($request, $server);
                break;
            case 'DELETE':
                $this->delete($request);
                break;
            default:
                return $this->response()->withStatus(405)->end();        }
    }

    /**
     * @param Request $request
     * @param array $server
     * @return bool
     */
    protected function post($request, $server)
    {
        $fileObjs = $request->getUploadedFile('files');
        if (!$fileObjs || count($fileObjs) == 0) {
            return $this->error(400, 'empty data');
        }

        $config = Config::getInstance()->getConf('UPLOAD');
        $fileAda = FileUp::getInstance($config)->setDataFormat(new ESInput());

        if (!isset($this->userData['isc']) || !$this->userData['isc']) {
            $fileAda->isChangeFilenameTrue();
        }

        //初始化上传器
        list($filesFin, $headers) = $fileAda->setFileType($this->fileType)->init($fileObjs, $server)->upload();

        // Outputting it, for example like this
        foreach ($headers as $header => $value) {
            $this->response()->withHeader($header, $value);
        }

        $output = new JqFileOutput($fileAda);
        return $this->success($output->formatData($filesFin));
    }

    /**
     * @return mixed
     */
    protected function get() {
        $config = Config::getInstance()->getConf('UPLOAD');
        $fileAda = FileList::getInstance($config)->setFileType($this->fileType);
        $files = $fileAda->createFiles();
        $output = new JqFileOutput($fileAda);
        return $this->success($output->formatData($files));
    }

    protected function head(array $server) {
        $this->response()->withHeader('Pragma', 'no-cache');
        $this->response()->withHeader('Cache-Control', 'no-store, no-cache, must-revalidate');
        $this->response()->withHeader('Content-Disposition', 'inline; filename="files.json"');
        // Prevent Internet Explorer from MIME-sniffing the content-type:
        $this->response()->withHeader('X-Content-Type-Options', 'nosniff');
        $this->response()->withHeader('Vary', 'Accept');
        if (isset($server['HTTP_ACCEPT']) && strpos($server['HTTP_ACCEPT'], 'application/json') !== false) {
            $this->response()->withHeader('Content-type', 'application/json');
        } else {
            $this->response()->withHeader('Content-type', 'text/plain');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function delete($request) {
        $fileName = $request->getQueryParam('file');
        $success = FileDelete::getInstance()->setFileType($this->fileType)->delete($fileName);
        return $this->writeCustomJson(200, [$fileName => $success]);
    }

    /**
     * 文件下载
     * @return mixed
     */
    public function download() {
        list($file, $headers) = FileDownload::getInstance()->setFileType($this->fileType)->getHeader('');
        if ($file === false) {
            return $this->response()->withStatus(404)->end();
        }
        foreach ($headers as $header => $value) {
            $this->response()->withHeader($header, $value);
        }
        return $this->response()->sendFile($file->getRealPath());
    }
}