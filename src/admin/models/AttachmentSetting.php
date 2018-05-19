<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\admin\models;

use Yii;
use yii\base\Model;

/**
 * Class AttachmentSetting
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class AttachmentSetting extends Model
{
    /**
     * @var string 附件存储卷
     */
    public $volume;

    /**
     * @var string 图片上传最大大小
     */
    public $imageMaxSize;

    /**
     * @var array 允许上传的图片文件
     */
    public $imageAllowFiles;

    /**
     * @var string 视频上传最大大小
     */
    public $videoMaxSize;

    /**
     * @var array 允许的视频后缀
     */
    public $videoAllowFiles;

    /**
     * @var string 文件上传最大大小
     */
    public $fileMaxSize;

    /**
     * @var array 允许的文件后缀
     */
    public $fileAllowFiles;

    /**
     * 定义字段类型
     * @return array
     */
    public function getTypes()
    {
        return [
            'volume' => 'string',
            'imageMaxSize' => 'string',
            'imageAllowFiles' => 'string',
            'videoMaxSize' => 'string',
            'videoAllowFiles' => 'string',
            'fileMaxSize' => 'string',
            'fileAllowFiles' => 'string',
        ];
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            [['volume', 'imageMaxSize', 'imageAllowFiles', 'videoMaxSize', 'videoAllowFiles', 'fileMaxSize', 'fileAllowFiles'], 'string'],
            ['volume', 'default', 'value' => 'public'],
            ['imageMaxSize', 'default', 'value' => '2M'],
            ['imageAllowFiles', 'default', 'value' => 'png,jpg,jpeg,gif,bmp'],
            ['videoMaxSize', 'default', 'value' => '100M'],
            ['videoAllowFiles', 'default', 'value' => 'flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid'],
            ['fileMaxSize', 'default', 'value' => '100M'],
            ['fileAllowFiles', 'default', 'value' => 'rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md,xml,xmind'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'volume' => Yii::t('yuncms', 'Store Volume'),
            'imageMaxSize' => Yii::t('yuncms', 'Image Max Size'),
            'imageAllowFiles' => Yii::t('yuncms', 'Image Allow Files'),
            'videoMaxSize' => Yii::t('yuncms', 'Video Max Size'),
            'videoAllowFiles' => Yii::t('yuncms', 'Video Allow Files'),
            'fileMaxSize' => Yii::t('yuncms', 'File Max Size'),
            'fileAllowFiles' => Yii::t('yuncms', 'File Allow Files'),
        ];
    }

    /**
     * 返回标识
     */
    public function formName()
    {
        return 'attachment';
    }
}