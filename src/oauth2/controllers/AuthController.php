<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\oauth2\controllers;

use Yii;
use yii\authclient\ClientInterface;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yuncms\filters\OAuth2Authorize;
use yuncms\web\Controller;
use yuncms\user\models\LoginForm;
use yuncms\user\models\UserSocialAccount;
use yuncms\oauth2\actions\Token;
use yii\authclient\AuthAction;

/**
 * Oauth2 登录控制器
 * @property bool $isOauthRequest
 * @property AuthAction $action
 * @method finishAuthorization()
 */
class AuthController extends Controller
{

    protected $rememberFor;

    public function behaviors()
    {
        return [
            'oauth2Auth' => [
                'class' => OAuth2Authorize::class,
                'only' => ['authorize'],
            ],
        ];
    }

    public function actions()
    {
        return [
            /**
             * Returns an access token.
             */
            'token' => [
                'class' => Token::class,
            ],
            /**
             * OPTIONAL
             * Third party oauth providers also can be used.
             */
            'back' => [
                'class' => AuthAction::class,
                'successCallback' => [$this, 'successCallback'],
            ],
        ];
    }

    /**
     * 初始化
     */
    public function init()
    {
        parent::init();
        $this->rememberFor = Yii::$app->settings->get('rememberFor', 'user');
    }

    /**
     * Display login form, signup or something else.
     * AuthClients such as Google also may be used
     */
    public function actionAuthorize()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            if ($this->isOauthRequest) {
                $this->finishAuthorization();
            } else {
                return $this->goBack();
            }
        } else {
            $this->layout = false;
            return $this->render('authorize', [
                'model' => $model,
            ]);
        }
    }

    /**
     * callback
     * @return string
     */
    public function actionCallback()
    {
        return '';
    }

    /**
     * OPTIONAL
     * Third party oauth callback sample
     * @param ClientInterface $client
     * @throws InvalidConfigException
     */
    public function successCallback(ClientInterface $client)
    {
        $account = UserSocialAccount::find()->byClient($client)->one();
        if ($account === null) {
            $account = UserSocialAccount::create($client);
        }
        if ($account->user instanceof Yii::$app->user->id) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t('oauth2', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/oauth2/auth/authorize']);
            } else {
                Yii::$app->user->login($account->user, $this->rememberFor);
                if ($this->isOauthRequest) {
                    $this->finishAuthorization();
                }
            }
        } else {
            $this->action->successUrl = Url::to(['/oauth2/auth/authorize']);
        }
    }
}