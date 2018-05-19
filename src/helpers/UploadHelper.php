<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\helpers;

use Yii;
use yuncms\filesystem\FilesystemAdapter;

/**
 * 文件上传助手
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class UploadHelper
{
    /**
     * 获取存储卷
     * @return \yuncms\filesystem\Cloud|\yuncms\filesystem\Filesystem|FilesystemAdapter
     * @throws \yii\base\InvalidConfigException
     */
    public static function getDisk()
    {
        return Yii::$app->filesystem->disk(Yii::$app->settings->get('volume', 'attachment', 'attachment'));
    }

    /**
     * 返回允许上传的最大大小单位 Byte
     * @param string $maxSize 最大上传大小MB
     * @return int the max upload size in Byte
     */
    public static function getMaxUploadByte($maxSize = null)
    {
        return self::getMaxUploadSize($maxSize) * 1024 * 1024;
    }

    /**
     * 返回允许上传的最大大小单位 MB
     * @param string $maxSize 最大上传大小MB
     * @return int the max upload size in MB
     */
    public static function getMaxUploadSize($maxSize = null)
    {
        $maxUpload = (int)(ini_get('upload_max_filesize'));
        $maxPost = (int)(ini_get('post_max_size'));
        $memoryLimit = (int)(ini_get('memory_limit'));
        $min = min($maxUpload, $maxPost, $memoryLimit);
        if ($maxSize) {
            $maxSize = (int)$maxSize;
            return min($maxSize, $min);
        }
        return $min;
    }

    /**
     * 获取允许上传的最大图像大小
     * @return int
     */
    public static function getImageMaxSizeByte()
    {
        $imageMaxSize = Yii::$app->getSettings()->get('imageMaxSize','attachment');
        return self::getMaxUploadByte($imageMaxSize);
    }

    /**
     * 获取允许上传的最大视频大小
     * @return int
     */
    public static function getVideoMaxSizeByte()
    {
        $videoMaxSize = Yii::$app->getSettings()->get('videoMaxSize','attachment');
        return self::getMaxUploadByte($videoMaxSize);
    }

    /**
     * 获取允许上传的最大文件大小
     * @return int
     */
    public static function getFileMaxSizeByte()
    {
        $fileMaxSize = Yii::$app->getSettings()->get('fileMaxSize','attachment');
        return self::getMaxUploadByte($fileMaxSize);
    }

    /**
     * 获取允许上传的图像 mimeTypes 列表
     * @return array ['image/jpg','image/png']
     */
    public static function getAcceptImageMimeTypes()
    {
        $imageAllowFiles = Yii::$app->getSettings()->get('imageAllowFiles','attachment');
        $extensions = explode(',', $imageAllowFiles);
        array_walk($extensions, function (&$value) {
            $value = 'image/' . $value;
        });
        return $extensions;
    }

    /**
     * 格式化后缀
     *
     * @param string $extensions 后缀数组 jpg,png,gif,bmp
     * @return mixed ['.jpg','.png']
     */
    public static function normalizeExtension($extensions)
    {
        $extensions = explode(',', $extensions);
        array_walk($extensions, function (&$value) {
            $value = '.' . $value;
        });
        return $extensions;
    }
}