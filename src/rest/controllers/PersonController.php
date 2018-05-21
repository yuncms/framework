<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\rest\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Url;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yuncms\rest\Controller;
use yuncms\rest\models\AvatarForm;
use yuncms\rest\models\NicknameForm;
use yuncms\rest\models\User;
use yuncms\rest\models\UserSettingsForm;
use yuncms\rest\models\UserRecoveryForm;
use yuncms\rest\models\UserRegistrationForm;
use yuncms\rest\models\UserEmailRegistrationForm;
use yuncms\rest\models\UserMobileRegistrationForm;
use yuncms\rest\models\UserBindMobileForm;
use yuncms\user\models\UserProfile;

/**
 * 个人接口
 * @package yuncms\rest\controllers
 */
class PersonController extends Controller
{
    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     * @return array the allowed HTTP verbs.
     */
    protected function verbs()
    {
        return [
            'extra' => ['GET'],
            'profile' => ['GET', 'PUT', 'PATCH'],
            'social' => ['GET'],
            'me' => ['GET'],
            'nickname' => ['POST', 'PUT', 'PATCH'],
            'register' => ['POST'],
            'mobile-register' => ['POST'],
            'email-register' => ['POST'],
            'password' => ['POST'],
            'recovery' => ['POST'],
            'avatar' => ['POST'],
            'identification' => ['POST', 'GET'],
            'notifications' => ['GET'],
        ];
    }

    /**
     * 读取用户扩展数据
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionExtra()
    {
        $user = $this->findModel(Yii::$app->user->id);
        return $user->extra->toArray();
    }

    /**
     * 获取个人扩展资料
     * @return UserProfile
     * @throws NotFoundHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionProfile()
    {
        if (($model = UserProfile::findOne(['user_id' => Yii::$app->user->identity->getId()])) !== null) {
            if (!Yii::$app->request->isGet) {
                $model->load(Yii::$app->getRequest()->getBodyParams(), '');
                if ($model->save() === false && !$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
                }
            }
            return $model;
        } else {
            throw new NotFoundHttpException ('User not found.');
        }
    }

    /**
     * 获取我绑定的社交媒体账户
     * @return \yuncms\user\models\UserSocialAccount[]
     * @throws NotFoundHttpException
     */
    public function actionSocial()
    {
        $user = $this->findModel(Yii::$app->user->id);
        return $user->getSocialAccounts();
    }

    /**
     * 获取个人基本资料
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionMe()
    {
        $user = $this->findModel(Yii::$app->user->id);
        return [
            'id' => $user->id,
            'username' => $user->username,
            'nickname' => $user->nickname,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'mobile_confirmed_at' => $user->mobile_confirmed_at,
            'faceUrl' => $user->faceUrl,
            'identified' => $user->identified,
            'transfer_balance' => $user->transfer_balance,
            'balance' => $user->balance
        ];
    }

    /**
     * @return bool|NicknameForm
     * @throws InvalidConfigException
     * @throws ServerErrorHttpException
     */
    public function actionNickname()
    {
        $model = new NicknameForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (($user = $model->save()) != false) {
            Yii::$app->getResponse()->setStatusCode(200);
            return $user;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * @return UserRegistrationForm|false|\yuncms\user\models\User
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRegister()
    {
        $model = new UserRegistrationForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (($user = $model->register()) != false) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($user->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            return $user;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 注册用户
     * @return UserMobileRegistrationForm|false|\yuncms\user\models\User
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionMobileRegister()
    {
        $model = new UserMobileRegistrationForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (($user = $model->register()) != false) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($user->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            return $user;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 注册用户
     * @return UserEmailRegistrationForm|\yuncms\user\models\User
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionEmailRegister()
    {
        $model = new UserEmailRegistrationForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (($user = $model->register()) != false) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($user->getPrimaryKey(true)));
            $response->getHeaders()->set('Location', Url::toRoute(['view', 'id' => $id], true));
            return $user;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 修改密码
     * @return UserSettingsForm
     * @throws ServerErrorHttpException
     */
    public function actionPassword()
    {
        $model = new UserSettingsForm();
        $model->load(Yii::$app->request->post(), '');
        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(200);
            return $model;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 重置密码
     * @return UserRecoveryForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRecovery()
    {
        $model = new UserRecoveryForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->resetPassword()) {
            Yii::$app->getResponse()->setStatusCode(200);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 上传头像
     * @return AvatarForm|bool
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAvatar()
    {
        $model = new AvatarForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (($model->save()) !== false) {
            Yii::$app->getResponse()->setStatusCode(200);
            return $model;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 绑定手机号
     * @return bool|User|UserBindMobileForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionBindMobile()
    {
        $model = new UserBindMobileForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if (($user = $model->bind()) != false) {
            Yii::$app->getResponse()->setStatusCode(200);
            return $user;
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
        return $model;
    }

    /**
     * 实名认证
     * @return \yuncms\identification\rest\models\Identification
     * @throws MethodNotAllowedHttpException
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIdentification()
    {
        if (!class_exists('yuncms\identification\rest\models\Identification')) {
            throw new InvalidConfigException('No identification module installed.');
        } else {
            if (Yii::$app->request->isPost) {
                $model = \yuncms\identification\rest\models\Identification::findByUserId(Yii::$app->user->getId());
                $model->scenario = \yuncms\identification\rest\models\Identification::SCENARIO_UPDATE;
                $model->load(Yii::$app->getRequest()->getBodyParams(), '');
                if (($model->save()) != false) {
                    $response = Yii::$app->getResponse();
                    $response->setStatusCode(201);
                    return $model;
                } elseif (!$model->hasErrors()) {
                    throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
                }
                return $model;
            } else if (Yii::$app->request->isGet) {
                return \yuncms\identification\rest\models\Identification::findByUserId(Yii::$app->user->getId());
            }
            throw new MethodNotAllowedHttpException();
        }
    }

    /**
     * 获取模型
     * @param int $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) != null) {
            return $model;
        } else {
            throw new NotFoundHttpException("Object not found: $id");
        }
    }
}