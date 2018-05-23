<?php

namespace yuncms\models;

use Yii;
use yii\base\Exception;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\web\IdentityInterface;
use yii\filters\RateLimitInterface;
use yii\behaviors\TimestampBehavior;
use yuncms\db\ActiveRecord;
use yuncms\helpers\PasswordHelper;
use yuncms\notifications\contracts\NotifiableInterface;
use yuncms\notifications\Notifiable;
use yuncms\oauth2\OAuth2IdentityInterface;
use yuncms\user\models\User;

/**
 * This is the model class for table "{{%user}}".
 *
 * Database fields:
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $mobile
 * @property string $nickname
 * @property string $auth_key
 * @property string $password_hash
 * @property string $access_token
 * @property integer $avatar
 * @property string $unconfirmed_email
 * @property string $unconfirmed_mobile
 * @property string $registration_ip
 * @property integer $flags
 * @property integer $email_confirmed_at
 * @property integer $mobile_confirmed_at
 * @property integer $blocked_at
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property string $password
 * @property-read boolean $isBlocked 账户是否锁定
 * @property-read bool $isMobileConfirmed 是否已经手机激活
 * @property string $authKey
 * @property-read bool $isEmailConfirmed 是否已经邮箱激活
 * @method touch($attribute) 刷新指定字段时间戳
 *
 */
class BaseUser extends ActiveRecord implements IdentityInterface, RateLimitInterface, NotifiableInterface, OAuth2IdentityInterface
{
    use Notifiable;

    // following constants are used on secured email changing process
    const OLD_EMAIL_CONFIRMED = 0b1;
    const NEW_EMAIL_CONFIRMED = 0b10;

    /**
     * @var string Plain password. Used for model validation.
     */
    public $password;

    /**
     * @var string Default username regexp
     */
    public static $usernameRegexp = '/^[-a-zA-Z0-9_]+$/u';

    /**
     * @var string Default nickname regexp
     */
    public static $nicknameRegexp = '/^[-a-zA-Z0-9_\x{4e00}-\x{9fa5}\.@]+$/u';

    /**
     * @var string Default mobile regexp
     */
    public static $mobileRegexp = '/^1[34578]{1}[\d]{9}$|^166[\d]{8}$|^19[89]{1}[\d]{8}$/';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * 定义行为
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username rules
            'usernameMatch' => ['username', 'match', 'pattern' => static::$usernameRegexp],
            'usernameLength' => ['username', 'string', 'min' => 3, 'max' => 50],
            'usernameUnique' => ['username', 'unique', 'message' => Yii::t('yuncms', 'This username has already been taken')],
            'usernameTrim' => ['username', 'trim'],

            // nickname rules
            'nicknameMatch' => ['nickname', 'match', 'pattern' => static::$nicknameRegexp],
            'nicknameLength' => ['nickname', 'string', 'min' => 3, 'max' => 255],
            'nicknameUnique' => ['nickname', 'unique', 'message' => Yii::t('yuncms', 'This nickname has already been taken')],
            'nicknameTrim' => ['nickname', 'trim'],

            // email rules
            'emailPattern' => ['email', 'email', 'checkDNS' => true],
            'emailLength' => ['email', 'string', 'max' => 255],
            'emailUnique' => ['email', 'unique', 'message' => Yii::t('yuncms', 'This email address has already been taken')],
            'emailTrim' => ['email', 'trim'],
            'emailDefault' => ['email', 'default', 'value' => null],

            //mobile rules
            'mobilePattern' => ['mobile', 'match', 'pattern' => static::$mobileRegexp],
            'mobileLength' => ['mobile', 'string', 'max' => 11],
            'mobileUnique' => ['mobile', 'unique', 'message' => Yii::t('yuncms', 'This phone has already been taken')],
            'mobileDefault' => ['mobile', 'default', 'value' => null],

            // password rules
            'passwordLength' => ['password', 'string', 'min' => 6],

            [['flags', 'email_confirmed_at', 'mobile_confirmed_at', 'blocked_at'], 'integer'],
            [['registration_ip'], 'string', 'max' => 255],
            [['mobile', 'unconfirmed_mobile'], 'string', 'max' => 11],
            [['access_token'], 'string', 'max' => 100],
            [['unconfirmed_email'], 'string', 'max' => 150],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yuncms', 'ID'),
            'username' => Yii::t('yuncms', 'Username'),
            'email' => Yii::t('yuncms', 'Email'),
            'mobile' => Yii::t('yuncms', 'Mobile'),
            'nickname' => Yii::t('yuncms', 'Nickname'),
            'auth_key' => Yii::t('yuncms', 'Auth Key'),
            'password_hash' => Yii::t('yuncms', 'Password Hash'),
            'access_token' => Yii::t('yuncms', 'Access Token'),
            'avatar' => Yii::t('yuncms', 'Avatar'),
            'unconfirmed_email' => Yii::t('yuncms', 'Unconfirmed Email'),
            'unconfirmed_mobile' => Yii::t('yuncms', 'Unconfirmed Mobile'),
            'registration_ip' => Yii::t('yuncms', 'Registration Ip'),
            'flags' => Yii::t('yuncms', 'Flags'),
            'email_confirmed_at' => Yii::t('yuncms', 'Email Confirmed At'),
            'mobile_confirmed_at' => Yii::t('yuncms', 'Mobile Confirmed At'),
            'blocked_at' => Yii::t('yuncms', 'Blocked At'),
            'created_at' => Yii::t('yuncms', 'Created At'),
            'updated_at' => Yii::t('yuncms', 'Updated At'),
        ];
    }

    /**
     * 设置Email已经验证
     * @return bool
     */
    public function setEmailConfirm()
    {
        return (bool)$this->updateAttributes(['email_confirmed_at' => time()]);
    }

    /**
     * 设置手机号已经验证
     * @return bool
     */
    public function setMobileConfirm()
    {
        return (bool)$this->updateAttributes(['mobile_confirmed_at' => time()]);
    }

    /**
     * 通过登陆邮箱或手机号获取用户
     * @param string $emailOrMobile
     * @return BaseUser|null
     */
    public static function findByEmailOrMobile($emailOrMobile)
    {
        if (filter_var($emailOrMobile, FILTER_VALIDATE_EMAIL)) {
            return static::findByEmail($emailOrMobile);
        } else if (preg_match(self::$mobileRegexp, $emailOrMobile)) {
            return static::findByMobile($emailOrMobile);
        }
        return null;
    }

    /**
     * 通过邮箱获取用户
     * @param string $email 邮箱
     * @return null|static
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * 通过手机号获取用户
     * @param string $mobile
     * @return static
     */
    public static function findByMobile($mobile)
    {
        return static::findOne(['mobile' => $mobile]);
    }

    /**
     * 通过用户名获取用户
     * @param string $username 用户标识
     * @return null|static
     */
    public static function findModelByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * 获取auth_key
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * 验证密码
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return PasswordHelper::validate($password, $this->password_hash);
    }

    /**
     * 验证AuthKey
     * @param string $authKey
     * @return boolean
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * 创建 "记住我" 身份验证Key
     * @return void
     * @throws Exception
     */
    public function generateAuthKey()
    {
        try {
            $this->auth_key = Yii::$app->security->generateRandomString(32);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 创建 "记住我" 身份验证Key
     * @return void
     * @throws Exception
     */
    public function generateAccessToken()
    {
        try {
            $this->access_token = Yii::$app->security->generateRandomString(32);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * 随机生成一个用户名
     */
    public function generateUsername()
    {
        if ($this->email) {
            $this->username = explode('@', $this->email)[0];
            if ($this->validate(['username'])) {
                return $this->username;
            }
        } else if ($this->nickname) {
            $this->username = Inflector::slug($this->nickname, '');
            if ($this->validate(['username'])) {
                return $this->username;
            }
        }
        // generate name like "user1", "user2", etc...
        while (!$this->validate(['username'])) {
            $row = (new Query())->from('{{%user}}')->select('MAX(id) as id')->one();
            $this->username = 'user' . ++$row['id'];
        }
        return $this->username;
    }

    /**
     * 重置密码
     *
     * @param string $password
     * @return boolean
     */
    public function resetPassword($password)
    {
        try {
            return (bool)$this->updateAttributes(['password_hash' => PasswordHelper::hash($password)]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 锁定用户
     * @return boolean
     */
    public function block()
    {
        try {
            return (bool)$this->updateAttributes(['blocked_at' => time(), 'auth_key' => Yii::$app->security->generateRandomString()]);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 解除用户锁定
     * @return boolean
     */
    public function unblock()
    {
        return (bool)$this->updateAttributes(['blocked_at' => null]);
    }

    /**
     * 返回用户是否已经锁定
     * @return boolean Whether the user is blocked or not.
     */
    public function getIsBlocked()
    {
        return $this->blocked_at != null;
    }

    /**
     * 返回用户邮箱是否已经激活
     * @return boolean Whether the user is confirmed or not.
     */
    public function getIsEmailConfirmed()
    {
        return $this->email_confirmed_at != null;
    }

    /**
     * 返回用户手机是否已经激活
     * @return boolean Whether the user is confirmed or not.
     */
    public function getIsMobileConfirmed()
    {
        return $this->mobile_confirmed_at != null;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if ($insert) {
            $this->generateAccessToken();
            $this->generateAuthKey();
            if (empty($this->username)) {
                $this->generateUsername();
            }
        }

        if (!empty($this->password)) {
            $this->password_hash = PasswordHelper::hash($this->password);
        }
        return true;
    }

    /////////// RateLimitInterface /////////////////////

    /**
     * Returns the maximum number of allowed requests and the window size.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the maximum number of allowed requests,
     * and the second element is the size of the window in seconds.
     */
    public function getRateLimit($request, $action)
    {
        $rateLimit = Yii::$app->settings->get('requestRateLimit', 'user', 60);
        return [$rateLimit, 60];
    }

    /**
     * Loads the number of allowed requests and the corresponding timestamp from a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @return array an array of two elements. The first element is the number of allowed requests,
     * and the second element is the corresponding UNIX timestamp.
     */
    public function loadAllowance($request, $action)
    {
        $allowance = Yii::$app->cache->get($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance');
        $allowanceUpdatedAt = Yii::$app->cache->get($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance_update_at');
        if ($allowance && $allowanceUpdatedAt) {
            return [$allowance, $allowanceUpdatedAt];
        } else {
            return [Yii::$app->settings->get('requestRateLimit', 'user', 60), time()];
        }
    }

    /**
     * Saves the number of allowed requests and the corresponding timestamp to a persistent storage.
     * @param \yii\web\Request $request the current request
     * @param \yii\base\Action $action the action to be executed
     * @param int $allowance the number of allowed requests remaining.
     * @param int $timestamp the current timestamp.
     */
    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        Yii::$app->cache->set($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance', $allowance, 60);
        Yii::$app->cache->set($action->controller->id . ':' . $action->id . ':' . $this->id . '_allowance_update_at', $timestamp, 60);
    }

    //////// NotifiableInterface /////////

    /**
     * 默认向用户推送时使用的渠道
     * @return array
     */
    public function viaChannels()
    {
        return ['database', 'mail', 'sms', 'push', 'wechat', 'alipay', 'dingtalk'];
    }

    /**
     * 返回给定通道的通知路由信息。
     * ```php
     * public function routeNotificationForMail() {
     *      return $this->email;
     * }
     * ```
     * @param $channel string
     * @return mixed
     */
    public function routeNotificationFor($channel)
    {
        if (method_exists($this, $method = 'routeNotificationFor' . Inflector::camelize($channel))) {
            return $this->{$method}();
        }
        switch ($channel) {
            case 'database':
                return [
                    'notifiable_id' => $this->id,
                    'notifiable_class' => User::class
                ];
            case 'cloudPush':
                return [
                    'target' => 'ACCOUNT',
                    'targetValue' => $this->id,
                ];
            case 'mail':
                return $this->email;
            case 'sms':
                return $this->mobile;
        }
        return false;
    }
}
