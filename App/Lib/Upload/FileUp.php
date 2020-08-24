<?php


namespace App\Lib\Upload;


use EasySwoole\Component\Singleton;
use FileUpload\FileUpload;
use FileUpload\Validator;
use FileUpload\PathResolver;
use FileUpload\FileSystem;
use FileUpload\FileNameGenerator;
use FileUpload\File;
use Exception;


class FileUp extends UploadAdapter
{
    use Singleton;

    //文件大小验证器
    private $sizeValidator;

    // The machine's filesystem
    private $fileSystem;

    /** @var FileUpload */
    private $fileUpload;

    //重复上传文件
    protected $repeatFile;

    //是否修改文件名
    protected $isChangeFilename = false;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->sizeValidator = new Validator\SizeValidator($this->conf['max_size']);
        $this->fileSystem = new FileSystem\Simple();
    }

    /**
     * @return $this
     */
    public function isChangeFilenameTrue()
    {
        $this->isChangeFilename = true;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRepeatFile()
    {
        return $this->repeatFile;
    }

    /**
     * 文件上传
     * @return array
     */
    public function upload()
    {
        $this->callback();

        // Doing the deed
        list($files, $headers) = $this->fileUpload->processAll();
        /** @var File $file */
        foreach ($files as $file) {
            if (!$file->error) {
                $this->repeatFile[] = $file;
            }
        }
        return [$this->repeatFile, $headers];
    }

    /**
     * 初始化上传器
     * @param $files
     * @param $server
     * @return $this
     */
    public function init($files, $server)
    {
        $this->repeatFile = [];

        $filesFormat = $this->dataFormat->formatData($files);

        // FileUploader itself
        $this->fileUpload = new FileUpload($filesFormat, $server);
        // Simple validation (max file size 2MB and only two allowed mime types)
        $typeValidator = new Validator\MimeTypeValidator($this->fileType->getMimeType());

        $pathResolver = new PathResolver\Simple(substr($this->uploadPath, 0, -1));
        // Adding it all together. Note that you can use multiple validators or none at all
        $this->fileUpload->setPathResolver($pathResolver);
        $this->fileUpload->setFileSystem($this->fileSystem);
        $this->fileUpload->addValidator($this->sizeValidator);
        $this->fileUpload->addValidator($typeValidator);

        if ($this->isChangeFilename) {
            $filenameGenerator = new FileNameGenerator\MD5();
            $this->fileUpload->setFileNameGenerator($filenameGenerator);

            foreach ($filesFormat['name'] as $name) {//检查是否重复文件
                $filename = pathinfo($name, PATHINFO_FILENAME);
                $extension = pathinfo($name, PATHINFO_EXTENSION);

                $md5ConcatenatedName = md5($filename) . "." . $extension;

                $filePath = $this->fileUpload->getPathResolver()->getUploadPath($md5ConcatenatedName);
                if ($this->fileUpload->getFileSystem()->doesFileExist($filePath)) {
                    $file = new File($filePath, $md5ConcatenatedName);
                    $file->completed = true;
                    $this->repeatFile[] = $file;
                }
            }
        } else {
            $filenameGenerator = new FileNameGenerator\Custom(function($source_name){
                return $source_name;
            });
            $this->fileUpload->setFileNameGenerator($filenameGenerator);
        }

        return $this;
    }

    /**
     * 初始化回调
     */
    private function callback(){
        //文件检查成功后回调，验证当前文件是否存在，若存在则先删除
        $this->fileUpload->addCallback('afterValidation', function (File $file) {
            if (!$this->fileType->validate($file, $this->uploadPath)) {
                throw new Exception('ext err');
            }
        });
        //上传完成回调
        $this->fileUpload->addCallback('completed', function (File $file) {
            $this->fileType->callback($file);
        });
    }
}