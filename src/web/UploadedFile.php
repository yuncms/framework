<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\web;

use Yii;
use yii\httpclient\Client;
use yii\validators\UrlValidator;
use yuncms\filesystem\FilesystemAdapter;
use yuncms\helpers\FileHelper;
use yuncms\helpers\StringHelper;
use yuncms\helpers\UploadHelper;
use yuncms\models\Attachment;
use League\Flysystem\AdapterInterface;

/**
 * Class UploadedFile
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 *
 * @property string $rename
 * @property string $mimeType
 */
class UploadedFile extends \yii\web\UploadedFile
{
    public $isUploadedFile;

    /**
     * The cache copy of the file's hash name.
     *
     * @var string
     */
    protected $hashName = null;

    /**
     * Get a filename for the file.
     *
     * @param  string $path
     * @return string
     * @throws \Exception
     */
    public function hashName($path = null)
    {
        if ($path) {
            $path = rtrim($path, '/') . '/';
        }
        $hash = $this->hashName ?: $this->hashName = StringHelper::randomString(40);
        return $path . $hash . '.' . $this->getExtension();
    }

    /**
     * 返回一个Base64加载的文件实例
     * @param string $fileName the file name.
     * @param string $base64Data the base64Data of the file base64 data.
     * @return null|UploadedFile the instance of the uploaded file.
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public static function getInstanceByBase64($fileName, $base64Data)
    {
        $fileData = base64_decode($base64Data);
        $baseName = basename($fileName);
        $tempPath = FileHelper::getTempFilePath($baseName);
        file_put_contents($tempPath, $fileData);
        return new static([
            'isUploadedFile' => false,
            'name' => $baseName,
            'tempName' => $tempPath,
            'type' => FileHelper::getMimeType($tempPath),
            'size' => strlen($fileData),
            'error' => UPLOAD_ERR_OK,
        ]);
    }

    /**
     * 返回一个从远程加载的文件实例
     * @param string $url the url of the file url path.
     * @return null|UploadedFile the instance of the uploaded file.
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\Exception
     */
    public static function getInstanceByRemote($url)
    {
        $validator = new UrlValidator();
        if ($validator->validate($url, $error)) {
            $url = str_replace("&amp;", "&", $url);
            $http = new Client();
            $response = $http->get($url)->send();
            if (!$response->isOk) {
                return null;
            } else {
                $baseName = basename($url);
                $tempPath = FileHelper::getTempFilePath($baseName);
                file_put_contents($tempPath, $response->content);
                return new static([
                    'isUploadedFile' => false,
                    'name' => $baseName,
                    'tempName' => $tempPath,
                    'type' => FileHelper::getMimeType($tempPath),
                    'size' => strlen($response->content),
                    'error' => UPLOAD_ERR_OK,
                ]);
            }
        } else {
            return null;
        }
    }

    /**
     * 返回一个从本地加载的文件实例
     * @param string $path the path of the file local path.
     * @return null|UploadedFile the instance of the uploaded file.
     * @throws \yii\base\InvalidConfigException
     */
    public static function getInstanceByLocal($path)
    {
        $fileContent = file_get_contents($path);
        return new static([
            'isUploadedFile' => false,
            'name' => basename($path),
            'tempName' => $path,
            'type' => FileHelper::getMimeType($path),
            'size' => strlen($fileContent),
            'error' => UPLOAD_ERR_OK,
        ]);
    }

    /**
     * 重命名文件
     * @return string
     * @throws \Exception
     */
    public function getRename()
    {
        return $this->hashName('uploads' . DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('md'));
    }

    /**
     * @return string file extension
     * @throws \yii\base\InvalidConfigException
     */
    public function getMimeType()
    {
        return FileHelper::getMimeType($this->tempName);
    }

    /**
     * 保存上传文件到模型
     * @return bool|Attachment
     * @throws \yii\base\ErrorException
     * @throws \yii\base\InvalidConfigException
     * @throws \Exception
     */
    public function save()
    {
        $filePath = $this->getRename();
        if (!UploadHelper::getDisk()->exists($filePath)) {
            $type = $this->getMimeType();
            $fileContent = FileHelper::readAndDelete($this->tempName);
            UploadHelper::getDisk()->put($filePath, $fileContent, [
                'visibility' => AdapterInterface::VISIBILITY_PUBLIC
            ]);
            $model = new Attachment([
                'filename' => basename($filePath),
                'original_name' => $this->name,
                'path' => $filePath,
                'size' => $this->size,
                'type' => $type,
            ]);
            if ($model->save()) {
                return $model;
            }
        }
        return false;
    }



    /**
     * Saves the uploaded file to a temp location.
     *
     * @param bool $deleteTempFile whether to delete the temporary file after saving.
     * If true, you will not be able to save the uploaded file again in the current request.
     * @return string|false the path to the temp file, or false if the file wasn't saved successfully
     * @see error
     * @throws \yii\base\Exception
     */
    public function saveAsTempFile(bool $deleteTempFile = true)
    {
        if ($this->error != UPLOAD_ERR_OK) {
            return false;
        }
        $tempPath = FileHelper::getTempFilePath($this->name);
        if (!$this->saveAs($tempPath, $deleteTempFile)) {
            return false;
        }
        return $tempPath;
    }


}