<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

namespace yuncms\rest\controllers;

use Yii;
use yii\base\DynamicModel;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
use yuncms\helpers\ArrayHelper;
use yuncms\notifications\models\DatabaseNotification;
use yuncms\rest\Controller;
use yuncms\rest\models\User;

/**
 * Class NotificationController
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class NotificationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     * @return array the allowed HTTP verbs.
     */
    protected function verbs()
    {
        return ArrayHelper::merge(parent::verbs(), [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'mark-read' => ['POST'],
        ]);
    }

    /**
     * 跟用户相关的通知列表
     * @return ActiveDataProvider|ActiveDataFilter
     * @throws NotFoundHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionIndex()
    {
        $user = $this->findModel(Yii::$app->user->id);
        $query = $user->getNotifications()->orderBy(new Expression("IF(ISNULL(read_at),0,1) asc"))->addOrderBy(['created_at' => SORT_DESC]);
        $requestParams = Yii::$app->getRequest()->getBodyParams();
        if (empty($requestParams)) {
            $requestParams = Yii::$app->getRequest()->getQueryParams();
        }
        $filter = null;
        $dataFilter = Yii::createObject([
            'class' => ActiveDataFilter::class,
            'searchModel' => function () {
                return (new DynamicModel(['verb' => null]))
                    ->addRule('verb', 'string');
            },
        ]);
        if ($dataFilter->load($requestParams)) {
            $filter = $dataFilter->build();
            if ($filter === false) {
                return $dataFilter;
            }
        }
        if ($filter) {
            $query->andWhere($filter);
        }
        return Yii::createObject([
            'class' => ActiveDataProvider::class,
            'query' => $query,
            'pagination' => [
                'params' => $requestParams
            ],
            'sort' => [
                'params' => $requestParams
            ],
        ]);
    }

    /**
     * 查看通知详情
     * @param string $id
     * @return DatabaseNotification
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $user = $this->findModel(Yii::$app->user->id);
        /** @var DatabaseNotification $notification */
        if (($notification = $user->getNotifications()->andWhere(['id' => $id])->one()) != null) {
            $notification->setRead();
            return $notification;
        } else {
            throw new NotFoundHttpException("Notification not found: $id");
        }
    }

    /**
     * 标记指定通知为已读
     */
    public function actionBatchMarkRead()
    {
        if (($ids = Yii::$app->request->post('ids', null)) != null) {
            foreach ($ids as $id) {
                DatabaseNotification::updateAll(['read_at' => time()], ['id' => $id, 'notifiable_id' => Yii::$app->user->id, 'notifiable_class' => \yuncms\user\models\User::class]);
            }

        }
        Yii::$app->getResponse()->setStatusCode(200);
    }

    /**
     * 标记所有通知为已读
     */
    public function actionMarkRead()
    {
        DatabaseNotification::updateAll(['read_at' => time()], ['notifiable_id' => Yii::$app->user->id, 'notifiable_class' => \yuncms\user\models\User::class]);
        Yii::$app->getResponse()->setStatusCode(200);
    }

    /**
     * 获取用户实例
     * @param integer $id
     * @return User
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) != null) {
            return $model;
        } else {
            throw new NotFoundHttpException("User not found: $id");
        }
    }
}