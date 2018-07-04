<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\user\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yuncms\user\Module;
use yuncms\user\models\User;
use yuncms\user\models\UserSocialAccount;
use yuncms\user\models\LoginForm;

/**
 * Controller that manages user authentication process.
 *
 * @property Module $module
 * @property AuthAction $action
 */
class SecurityController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'auth', 'wechat-auth', 'blocked'],
                        'roles' => ['?']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['login', 'wechat-auth', 'auth', 'logout'],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'auth' => [
                'class' => AuthAction::class,
                // 如果用户未登录，将尝试登录，否则将尝试连接到用户的社交账户。
                'successCallback' => Yii::$app->user->getIsGuest() ? [$this, 'authenticate'] : [$this, 'connect']
            ],
            'wechat-auth' => [
                'class' => \xutl\wechat\oauth\AuthAction::class,
                'successCallback' => [$this, 'authenticate']
            ]
        ];
    }

    /**
     * 登录
     * @return array|string|Response
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            Yii::$app->session->setFlash('danger', Yii::t('yuncms', 'You are already logged in.'));
            return $this->goHome();
        }
        if (Yii::$app->request->isGet) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
        }
        if (Yii::$app->has('wechat') && Yii::$app->request->isWeChat) {
            return $this->redirect(['wechat-auth']);
        }

        /**
         * @var LoginForm $model
         */
        $model = new LoginForm();
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->login()) {
            return $this->goBack(Yii::$app->getHomeUrl());
        }
        return $this->render('login', ['model' => $model]);
    }

    /**
     * 退出用户后重定向到主页
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->getUser()->logout();
        Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());
        return $this->goBack();
    }

    /**
     * Tries to authenticate user via social network. If user has already used
     * this network's account, he will be logged in. Otherwise, it will try
     * to create new user account.
     *
     * @param ClientInterface $client
     * @throws \yii\base\Exception
     */
    public function authenticate(ClientInterface $client)
    {
        $account = UserSocialAccount::find()->byClient($client)->one();
        if ($account === null) {
            $account = UserSocialAccount::createClient($client);
        }
        $userAttributes = $client->getUserAttributes();
        if ($account->user instanceof User) {
            if ($account->user->isBlocked) {
                Yii::$app->session->setFlash('danger', Yii::t('yuncms', 'Your account has been blocked.'));
                $this->action->successUrl = Url::to(['/user/security/login']);
            } else {
                Yii::$app->user->login($account->user, Yii::$app->settings->get('rememberFor', 'user'));
                $this->action->successUrl = Yii::$app->getUser()->getReturnUrl();
            }
        } else if (in_array($client->getName(), ['wechat', 'wechat_mini', 'wechat_mobile','wechat_pub'])) {
            if (($wechat = UserSocialAccount::find()->byProvider('wechat')->andWhere(['client_id' => $userAttributes['id']])->one()) != null) {//网页扫码绑定
                $account->connect($wechat->user);
            } else if (($wechat = UserSocialAccount::find()->byProvider('wechat_mini')->andWhere(['client_id' => $userAttributes['id']])->one()) != null) {//小程序绑定
                $account->connect($wechat->user);
            } else if (($wechat = UserSocialAccount::find()->byProvider('wechat_mobile')->andWhere(['client_id' => $userAttributes['id']])->one()) != null) {//公众号绑定
                $account->connect($wechat->user);
            }
            Yii::$app->user->login($account->user, Yii::$app->settings->get('rememberFor', 'user'));
            $this->action->successUrl = Yii::$app->getUser()->getReturnUrl();
        } else {
            $this->action->successUrl = $account->getConnectUrl();
        }
    }

    /**
     * 尝试将社交账号连接到用户
     *
     * @param ClientInterface $client
     * @throws \yii\base\InvalidConfigException
     */
    public function connect(ClientInterface $client)
    {
        /**
         * @var UserSocialAccount $account
         */
        $account = new UserSocialAccount();
        $account->connectWithUser($client);
        $this->action->successUrl = Url::to(['/user/settings/networks']);
    }
}
