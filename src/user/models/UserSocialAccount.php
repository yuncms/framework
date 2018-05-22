<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\user\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Url;
use yii\authclient\ClientInterface as BaseClientInterface;
use yuncms\authclient\ClientInterface;
use yuncms\db\ActiveRecord;
use yuncms\helpers\Json;

/**
 * This is the model class for table "{{%user_social_account}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property string $email
 * @property string $provider
 * @property string $client_id
 * @property string $code
 * @property integer $created_at
 * @property string $data
 *
 * @property User $user
 *
 * @property-read string $connectUrl 关联Url
 * @property-read array $decodedData 开放平台响应数组
 * @property-read bool isAuthor 是否是作者
 * @property-read boolean $isDraft 是否草稿
 * @property bool $isConnected 是否已经连接
 */
class UserSocialAccount extends ActiveRecord
{
    /**
     * @var
     */
    private $_data;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_social_account}}';
    }

    /**
     * 定义行为
     * @return array
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['timestamp'] = [
            'class' => TimestampBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at']
            ],
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('yuncms', 'ID'),
            'user_id' => Yii::t('yuncms', 'Uer ID'),
            'username' => Yii::t('yuncms', 'Username'),
            'email' => Yii::t('yuncms', 'EMail'),
            'provider' => Yii::t('yuncms', 'Provider'),
            'client_id' => Yii::t('yuncms', 'Client Id'),
            'code' => Yii::t('yuncms', 'Code'),
            'created_at' => Yii::t('yuncms', 'Created At'),
            'data' => Yii::t('yuncms', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return boolean Whether this social account is connected to user.
     */
    public function getIsConnected()
    {
        return $this->user_id != null;
    }

    /**
     * Returns connect url.
     * @return string
     * @throws \yii\base\Exception
     */
    public function getConnectUrl()
    {
        $code = Yii::$app->security->generateRandomString();
        $this->updateAttributes(['code' => md5($code)]);
        return Url::to(['/user/registration/connect', 'code' => $code]);
    }

    public function connect(User $user)
    {
        return $this->updateAttributes(['username' => null, 'email' => null, 'code' => null, 'user_id' => $user->id]);
    }

    /**
     * @return mixed Json decoded properties.
     */
    public function getDecodedData()
    {
        if ($this->_data == null) {
            $this->_data = Json::decode($this->data);
        }
        return $this->_data;
    }

    /**
     * 通过用户ID查询用户微信绑定信息
     * @param int $userId
     * @param string $provider
     * @return null|array
     */
    public static function findProviderDataByUserId($userId, $provider)
    {
        $weChat = static::findOne(['user_id' => $userId, 'provider' => $provider]);
        if ($weChat) {
            return $weChat->getDecodedData();
        }
        return null;
    }

    /**
     * @param BaseClientInterface $client
     * @return object|UserSocialAccount
     * @throws \yii\base\InvalidConfigException
     */
    public static function createClient(BaseClientInterface $client)
    {
        /** @var UserSocialAccount $account */
        $account = Yii::createObject([
            'class' => static::class,
            'provider' => $client->getId(),
            'client_id' => $client->getUserAttributes()['id'],
            'data' => json_encode($client->getUserAttributes())
        ]);

        if ($client instanceof ClientInterface) {
            $account->setAttributes(['username' => $client->getUsername(), 'email' => $client->getEmail()], false);
        } else if ($client instanceof BaseClientInterface) {
            $account->setAttributes($client->getUserAttributes(), false);
        }

        if (!empty($account->email) && ($user = static::fetchUser($account)) instanceof User) {
            $account->user_id = $user->id;
        }

        $account->save(false);

        return $account;
    }

    /**
     * Tries to find an account and then connect that account with current user.
     *
     * @param BaseClientInterface $client
     * @throws \yii\base\InvalidConfigException
     */
    public static function connectWithUser(BaseClientInterface $client)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('danger', Yii::t('yuncms', 'Something went wrong'));
            return;
        }

        $account = static::fetchAccount($client);

        if ($account->user === null) {
            $account->link('user', Yii::$app->user->identity);
            Yii::$app->session->setFlash('success', Yii::t('yuncms', 'Your account has been connected'));
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('yuncms', 'This account has already been connected to another user'));
        }
    }

    /**
     * Tries to find account, otherwise creates new account.
     *
     * @param BaseClientInterface $client
     *
     * @return UserSocialAccount
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchAccount(BaseClientInterface $client)
    {
        $account = UserSocialAccount::find()->byClient($client)->one();
        if (null === $account) {
            $account = Yii::createObject(['class' => static::class, 'provider' => $client->getId(), 'client_id' => $client->getUserAttributes()['id'], 'data' => json_encode($client->getUserAttributes())]);
            $account->save(false);
        }

        return $account;
    }

    /**
     * Tries to find user or create a new one.
     *
     * @param UserSocialAccount $account
     *
     * @return User|boolean False when can't create user.
     * @throws \yii\base\InvalidConfigException
     */
    protected static function fetchUser(UserSocialAccount $account)
    {
        $user = User::findByEmail($account->email);
        if (null !== $user) {
            return $user;
        }
        /** @var \yuncms\user\models\User $user */
        $user = Yii::createObject([
            'class' => User::class,
            'scenario' => User::SCENARIO_CONNECT,
            'nickname' => $account->username,
            'email' => $account->email
        ]);

        if (!$user->validate(['email'])) {
            $account->email = null;
        }

        if (!$user->validate(['nickname'])) {
            $account->username = null;
        }
        return $user->createUser() ? $user : false;
    }

    /**
     * @inheritdoc
     * @return UserSocialAccountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserSocialAccountQuery(get_called_class());
    }
}
