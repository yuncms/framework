<?php
/**
 * @link https://github.com/borodulin/yii2-oauth2-server
 * @copyright Copyright (c) 2015 Andrey Borodulin
 * @license https://github.com/borodulin/yii2-oauth2-server/blob/master/LICENSE
 */

namespace yuncms\oauth2\grant\types;

use Yii;
use yii\db\Query;
use yii\helpers\Inflector;
use yii\base\InvalidConfigException;
use yii\web\ServerErrorHttpException;
use yuncms\oauth2\GrantType;
use yuncms\oauth2\models\OAuth2AccessToken;
use yuncms\oauth2\models\OAuth2RefreshToken;
use yuncms\user\models\User;
use yuncms\user\models\UserSocialAccount;
use yuncms\jobs\SocialAvatarDownloadJob;

/**
 * For example, the client makes the following HTTP request using
 * transport-layer security (with extra line breaks for display purposes
 * only):
 *
 * ```
 * POST /token HTTP/1.1
 * Host: server.example.com
 * Authorization: Basic czZCaGRSa3F0MzpnWDFmQmF0M2JW
 * Content-Type: application/x-www-form-urlencoded
 *
 * response_type=token&code=johndoe
 * ```
 *
 * @link https://tools.ietf.org/html/rfc6749#section-4.3
 * @author Dmitry Fedorenko
 *
 * @property array $responseData
 * @property null|\yuncms\user\models\User|object $user
 */
class WeChatMiniCredentials extends GrantType
{
    /** @var  \yuncms\user\models\User */
    private $_user;

    /**
     * Value MUST be set to "wechat"
     * @var string
     */
    public $grant_type;

    /**
     * The resource wechat authorization_code.
     * @var string
     */
    public $code;

    /**
     * Access Token Scope
     * @link https://tools.ietf.org/html/rfc6749#section-3.3
     * @var string
     */
    public $scope;

    /**
     * @var string
     */
    public $client_id;

    /**
     * @var string
     */
    public $client_secret;

    /**
     * @var \xutl\wechat\Wechat
     */
    private $wechat;

    /**
     * 初始化模型
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!Yii::$app->has('wechat')) {
            throw new InvalidConfigException("Unknown component ID: wechat.");
        }
        $this->wechat = Yii::$app->wechat;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['grant_type', 'client_id', 'code'], 'required'],
            ['grant_type', 'required', 'requiredValue' => 'wechat_mini'],
            [['client_id'], 'string', 'max' => 80],
            [['client_id'], 'validateClientId'],
            [['client_secret'], 'validateClientSecret'],
            [['scope'], 'validateScope'],
        ];
    }

    /**
     * @return array
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     * @throws \yii\base\Exception
     * @throws \yii\web\HttpException
     * @throws \yuncms\oauth2\Exception
     */
    public function getResponseData()
    {
        /** @var \yuncms\user\models\User $identity */
        $identity = $this->getUser();

        $accessToken = OAuth2AccessToken::createAccessToken([
            'client_id' => $this->client_id,
            'user_id' => $identity->id,
            'expires' => $this->accessTokenLifetime + time(),
            'scope' => $this->scope,
        ]);

        $refreshToken = OAuth2RefreshToken::createRefreshToken([
            'client_id' => $this->client_id,
            'user_id' => $identity->id,
            'expires' => $this->refreshTokenLifetime + time(),
            'scope' => $this->scope,
        ]);
        return [
            'access_token' => $accessToken->access_token,
            'expires_in' => $this->accessTokenLifetime,
            'token_type' => $this->tokenType,
            'scope' => $this->scope,
            'refresh_token' => $refreshToken->refresh_token,
        ];
    }

    /**
     * @return null|object|\yuncms\user\models\User
     * @throws ServerErrorHttpException
     * @throws \yii\web\HttpException
     * @throws InvalidConfigException
     * @throws \yii\authclient\InvalidResponseException
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $client = $this->wechat->miniProgram;
            $client->useOpenId = false;//使用unionid
            $client->validateAuthState = false;
            $token = $client->fetchAccessToken($this->code);
            $tokenParams = $token->getParams();
            if (isset($tokenParams['errcode'])) {
                throw new ServerErrorHttpException($tokenParams['errmsg'], $tokenParams['errcode']);
            }

            $account = UserSocialAccount::find()->byClient($client)->one();
            if ($account === null) {
                $account = UserSocialAccount::createClient($client);
            }
            if ($account->user instanceof User) {
                $this->_user = $account->user;
            } else {
                if (($wechat = UserSocialAccount::find()->byProvider('wechat')->andWhere(['client_id' => $client->getUserAttributes()['id']])->one()) != null) {//如果已经绑定了微信了
                    $account->connect($wechat->user);
                } else if (($wechat = UserSocialAccount::find()->byProvider('wechat_pub')->andWhere(['client_id' => $client->getUserAttributes()['id']])->one()) != null) {//绑定了公众号了
                    $account->connect($wechat->user);
                } else if (($wechat = UserSocialAccount::find()->byProvider('wechat_open')->andWhere(['client_id' => $client->getUserAttributes()['id']])->one()) != null) {//绑定了公众号了
                    $account->connect($wechat->user);
                } else {
                    /** @var \yuncms\user\models\User $user */
                    $user = Yii::createObject([
                        'class' => User::class,
                        'scenario' => User::SCENARIO_CONNECT,
                        'nickname' => $account->username,
                    ]);

                    // generate nickname like "user1", "user2", etc...
                    while (!$user->validate(['nickname'])) {
                        $row = (new Query())->from('{{%user}}')->select('MAX(id) as id')->one();
                        $user->nickname = Inflector::slug($account->username, '') . ++$row['id'];
                    }

                    if ($user->createUser()) {
                        $account->connect($user);
                    }
                    if ($user->hasErrors()) {
                        throw new ServerErrorHttpException('Failed to login the user for unknown reason.');
                    }
                    $this->_user = $user;
                }
            }
            if (!$this->_user->isAvatar) {
                Yii::$app->queue->push(new SocialAvatarDownloadJob(['user_id' => $this->_user->id, 'faceUrl' => $client->getUserAttributes()['headimgurl']]));
            }
        }
        return $this->_user;
    }
}
