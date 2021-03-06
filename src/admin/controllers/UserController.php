<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\admin\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yuncms\user\models\User;
use yuncms\user\models\UserProfile;
use yuncms\admin\models\UserSearch;

/**
 * Class UserController
 * @package yuncms\user\backend\controllers
 */
class UserController extends Controller
{
    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'confirm' => ['post'],
                    'block' => ['post'],
                ],
            ]
        ];
    }

    public function actions()
    {
        return [
            //....
            'settings' => [
                'class' => 'yuncms\actions\SettingsAction',
                'modelClass' => 'yuncms\admin\models\UserSettings',
                //'scenario' => 'user',
                //'scenario' => 'site', // Change if you want to re-use the model for multiple setting form.
                'viewName' => 'settings'    // The form we need to render
            ],
            //....
        ];
    }

    /**
     * 用户管理首页
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        Url::remember('', 'actions-redirect');
        $searchModel = Yii::createObject(UserSearch::class);
        $dataProvider = $searchModel->search(Yii::$app->request->get());

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionCreate()
    {
        /** @var User $model */
        $model = Yii::createObject([
            'class' => User::class,
            'scenario' => User::SCENARIO_CREATE,
        ]);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->createUser()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'User has been created'));
            return $this->redirect(['update', 'id' => $model->id]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        Url::remember('', 'actions-redirect');
        $model = $this->findModel($id);
        $model->scenario = User::SCENARIO_UPDATE;
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'Account details have been updated'));
            return $this->refresh();
        }
        return $this->render('_account', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing profile.
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionUpdateProfile($id)
    {
        Url::remember('', 'actions-redirect');
        $model = $this->findModel($id);
        $profile = $model->profile;
        if ($profile == null) {
            $profile = Yii::createObject(UserProfile::class);
            $profile->link('user', $model);
        }
        if (Yii::$app->request->isAjax && $profile->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($profile);
        }
        if ($profile->load(Yii::$app->request->post()) && $profile->save()) {
            Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'Profile details have been updated'));
            return $this->refresh();
        }

        return $this->render('_profile', [
            'model' => $model,
            'profile' => $profile,
        ]);
    }

    /**
     * Shows information about user.
     *
     * @param int $id
     *
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        Url::remember('', 'actions-redirect');
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Confirms the User.
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->setEmailConfirm();
        Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'User has been confirmed'));
        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'User has been deleted'));
        return $this->redirect(['index']);
    }

    /**
     * Blocks the user.
     *
     * @param int $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionBlock($id)
    {
        $user = $this->findModel($id);
        if ($user->getIsBlocked()) {
            $user->unblock();
            Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'User has been unblocked'));
        } else {
            $user->block();
            Yii::$app->getSession()->setFlash('success', Yii::t('yuncms', 'User has been blocked'));
        }
        return $this->redirect(Url::previous('actions-redirect'));
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param int $id
     *
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $user = User::findOne($id);
        if ($user === null) {
            throw new NotFoundHttpException(Yii::t('yuncms', 'The requested page does not exist.'));
        }
        return $user;
    }
}