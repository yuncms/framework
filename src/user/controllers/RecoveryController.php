<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\user\controllers;

use Yii;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yuncms\web\Response;
use yuncms\web\Controller;
use yuncms\user\models\UserToken;
use yuncms\user\models\RecoveryForm;

/**
 * RecoveryController manages password recovery process.
 *
 * @property \yuncms\user\Module $module
 */
class RecoveryController extends Controller
{
    protected $enablePasswordRecovery;

    /** @inheritdoc */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['request', 'reset'],
                        'roles' => ['?']
                    ]
                ]
            ]
        ];
    }

    public function init()
    {
        parent::init();
        $this->enablePasswordRecovery = Yii::$app->settings->get('enablePasswordRecovery', 'user');
    }

    /**
     * 显示找回密码页面
     * @return array|string|Response
     */
    public function actionRequest()
    {
        if (!$this->enablePasswordRecovery) {
            return $this->redirect(['/user/security/login']);
        }
        /** @var RecoveryForm $model */
        $model = new RecoveryForm(['scenario' => 'request']);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            return $this->redirect(['/user/recovery/request']);
        }
        return $this->render('request', ['model' => $model]);
    }

    /**
     * 显示重置密码页面
     * @param int $id
     * @param string $code
     * @return array|string|Response
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionReset($id, $code)
    {
        if (!$this->enablePasswordRecovery) {
            return $this->redirect(['/user/security/login']);
        }
        /** @var UserToken $token */
        $token = UserToken::findOne(['user_id' => $id, 'code' => $code, 'type' => UserToken::TYPE_RECOVERY]);
        if ($token === null || $token->isExpired || $token->user === null) {
            Yii::$app->session->setFlash('danger', Yii::t('yuncms', 'Recovery link is invalid or expired. Please try requesting a new one.'));
            return $this->render('/message', [
                'title' => Yii::t('yuncms', 'Invalid or expired link'),
                'module' => $this->module
            ]);
        }
        /** @var RecoveryForm $model */
        $model = new RecoveryForm(['scenario' => 'reset']);
        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            return $this->redirect(['/user/security/login']);
        }

        return $this->render('reset', ['model' => $model]);
    }
}
