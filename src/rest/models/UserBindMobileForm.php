<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\rest\models;

use Yii;
use yii\base\Model;
use yuncms\validators\MobileValidator;

/**
 * 绑定手机号
 */
class UserBindMobileForm extends Model
{
    /**
     * @var string
     */
    public $mobile;

    /**
     * @var string 验证码
     */
    public $verifyCode;

    /**
     * @var User
     */
    protected $user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'mobileTrim' => ['mobile', 'filter', 'filter' => 'trim'],
            'mobileRequired' => ['mobile', 'required'],
            'mobilePattern' => ['mobile', MobileValidator::class],
            'mobileUnique' => ['mobile', 'unique', 'targetClass' => User::class, 'message' => Yii::t('yuncms', 'There is user with this mobile.')],

            // verifyCode needs to be entered correctly
            'verifyCodeRequired' => ['verifyCode', 'required'],
            'verifyCodeString' => ['verifyCode', 'string', 'min' => 5, 'max' => 7],
            'verifyCodeValidator' => ['verifyCode',
                'yuncms\sms\captcha\CaptchaValidator',
                'captchaAction' => '/sms/verify-code',
                'skipOnEmpty' => false,
                'message' => Yii::t('yuncms', 'Phone verification code input error.')
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'mobile' => Yii::t('yuncms', 'Mobile'),
            'verifyCode' => Yii::t('yuncms', 'verifyCode')
        ];
    }

    /**
     * 绑定手机
     * @return bool|User
     */
    public function bind()
    {
        if ($this->validate() && ($user = $this->getUser()) != null) {
            $user->updateAttributes(['mobile' => $this->mobile, 'mobile_confirmed_at' => time()]);
            return $user;
        }
        return false;
    }

    /**
     * @return User|null
     */
    public function getUser()
    {
        return User::findOne(Yii::$app->user->getId());
    }
}