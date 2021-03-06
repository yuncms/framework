<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\admin\controllers;

use Yii;
use yii\web\Response;
use yii\filters\VerbFilter;
use yuncms\web\Controller;
use yii\web\NotFoundHttpException;
use yuncms\user\models\UserAssignment;
use yuncms\admin\models\UserAssignmentSearch;

/**
 * AssignmentController implements the CRUD actions for Assignment model.
 */
class UserAssignmentController extends Controller
{
    public $userClassName;
    public $idField = 'id';
    public $usernameField = 'username';
    public $fullNameField;
    public $extraColumns = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->userClassName === null) {
            $this->userClassName = 'yuncms\user\models\User';
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'assign' => ['post'],
                    'revoke' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Assignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAssignmentSearch;
        $dataProvider = $searchModel->search(Yii::$app->getRequest()->getQueryParams(), $this->userClassName, $this->usernameField);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'idField' => $this->idField,
            'usernameField' => $this->usernameField,
            'extraColumns' => $this->extraColumns,
        ]);
    }

    /**
     * Displays a single Assignment model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
            'idField' => $this->idField,
            'usernameField' => $this->usernameField,
            'fullNameField' => $this->fullNameField,
        ]);
    }

    /**
     * Assign items
     * @param string $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAssign($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $model = new UserAssignment($id);
        $success = $model->assign($items);
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return array_merge($model->getItems(), ['success' => $success]);
    }

    /**
     * Assign items
     * @param string $id
     * @return array
     * @throws \yii\base\InvalidConfigException
     */
    public function actionRevoke($id)
    {
        $items = Yii::$app->getRequest()->post('items', []);
        $model = new UserAssignment($id);
        $success = $model->revoke($items);
        Yii::$app->getResponse()->format = Response::FORMAT_JSON;
        return array_merge($model->getItems(), ['success' => $success]);
    }

    /**
     * Finds the Assignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        /** @var \yii\web\IdentityInterface $class */
        $class = $this->userClassName;
        if (($user = $class::findIdentity($id)) !== null) {
            return new UserAssignment($id, $user);
        } else {
            throw new NotFoundHttpException(Yii::t('yuncms', 'The requested page does not exist.'));
        }
    }
}