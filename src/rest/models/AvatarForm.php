<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\rest\models;

use Yii;
use yii\base\ErrorException;
use yii\base\Model;
use yuncms\helpers\AvatarHelper;
use yuncms\web\UploadedFile;

/**
 * Class AvatarForm
 * @package api\modules\v1\models
 *
 * @property \yuncms\user\models\User $user
 */
class AvatarForm extends Model
{
    /**
     * @var UploadedFile 头像上传字段
     */
    public $file;

    /**
     * @var string
     */
    public $faceUrl;

    /**
     * @var \yuncms\user\models\User
     */
    private $_user;

    /**
     * @var string
     */
    private $_originalImage;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'gif, jpg, png', 'maxSize' => 1024 * 1024 * 2, 'tooBig' => Yii::t('yuncms', 'File has to be smaller than 2MB')],
        ];
    }

    /**
     * 保存头像
     *
     * @return boolean
     */
    public function save()
    {
        if ($this->validate()) {
            try {
                if (AvatarHelper::save($this->getUser(), $this->_originalImage)) {
                    $this->file = null;
                    $this->faceUrl = AvatarHelper::getAvatar($this->getUser());
                    return true;
                } else {
                    return false;
                }
            } catch (ErrorException $e) {
                $this->addError('file', $e->getMessage());
            } catch (\Exception $e) {
                $this->addError('file', $e->getMessage());
            }
        }
        return false;
    }

    public function beforeValidate()
    {
        $this->file = UploadedFile::getInstanceByName('file');
        return parent::beforeValidate();
    }

    /**
     * 验证后保存原图
     * @throws \yii\base\Exception
     */
    public function afterValidate()
    {
        //保存原图
        if (($this->_originalImage = $this->file->saveAsTempFile()) == false) {
            $this->addError('file', Yii::t('yuncms', 'Failed to save the image.'));
        }
        parent::afterValidate();
    }

    /*
     * @return User
     */
    public function getUser()
    {
        if ($this->_user == null) {
            $this->_user = Yii::$app->user->identity;
        }
        return $this->_user;
    }
}