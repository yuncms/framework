<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\models;

use Yii;
use yii\db\BaseActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\AttributeBehavior;
use yuncms\helpers\UploadHelper;
use yuncms\jobs\AttachmentDeleteJob;
use yuncms\behaviors\IpBehavior;
use yuncms\db\ActiveRecord;
use yuncms\user\models\User;

/**
 * Class Attachment
 * @property int $id
 * @property int $user_id 上传用户uID
 * @property string $filename 文件名
 * @property string $original_name 文件原始名称
 * @property string $model 上传模型
 * @property string $hash 文件哈希
 * @property int $size 文件大小
 * @property string $type 文件类型
 * @property string $mine_type 文件类型
 * @property string $ext 文件后缀
 * @property string $path 存储路径
 * @property string $ip 用户IP
 * @property int $created_at 创建时间
 *
 * @property-read string $url WEB访问路径
 * @property-read User $user
 *
 * @package yuncms\models
 */
class Attachment extends ActiveRecord
{

    /**
     * 定义数据表
     */
    public static function tableName()
    {
        return '{{%attachment}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
            [
                'class' => IpBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ip'
                ]
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'user_id'
                ],
                'value' => function () {
                    return Yii::$app->user->id;
                }
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yuncms', 'ID'),
            'user_id' => Yii::t('yuncms', 'User Id'),
            'filename' => Yii::t('yuncms', 'Filename'),
            'original_name' => Yii::t('yuncms', 'Original FileName'),
            'size' => Yii::t('yuncms', 'File Size'),
            'type' => Yii::t('yuncms', 'File Type'),
            'path' => Yii::t('yuncms', 'Path'),
            'ip' => Yii::t('yuncms', 'User Ip'),
            'created_at' => Yii::t('yuncms', 'Created At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'original_name'], 'required'],
        ];
    }

    /**
     * User Relation
     * @return \yii\db\ActiveQueryInterface
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * 获取访问Url
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function getUrl()
    {
        return UploadHelper::getDisk()->url($this->path);
    }

    /**
     * 保存前修正附件路径
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->path = str_replace('\\', '/', $this->path);
        return parent::beforeSave($insert);
    }

    /**
     * 附件删除
     * @return mixed
     */
    public function afterDelete()
    {
        Yii::$app->queue->push(new AttachmentDeleteJob([
            'path' => $this->path
        ]));
        return parent::afterDelete();
    }
}