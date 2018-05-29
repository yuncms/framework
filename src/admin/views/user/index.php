<?php

use yii\helpers\Url;
use yii\web\View;
use yii\data\ActiveDataProvider;
use yuncms\grid\GridView;
use yuncms\helpers\Html;
use yuncms\admin\widgets\Box;
use yuncms\admin\widgets\Toolbar;
use yuncms\admin\widgets\Alert;
use yuncms\admin\models\UserSearch;

/**
 * @var View $this
 * @var ActiveDataProvider $dataProvider
 * @var UserSearch $searchModel
 */

$this->title = Yii::t('yuncms', 'Manage Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?= Alert::widget() ?>
            <?php Box::begin([
                'header' => Html::encode($this->title),
            ]); ?>
            <div class="row">
                <div class="col-sm-4 m-b-xs">
                    <?= Toolbar::widget(['items' => [
                        [
                            'label' => Yii::t('yuncms', 'Manage User'),
                            'url' => ['/admin/user/index'],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Create User'),
                            'url' => ['/admin/user/create'],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Settings'),
                            'url' => ['/admin/user/settings'],
                        ],
                    ]]); ?>
                </div>
                <div class="col-sm-8 m-b-xs">

                </div>
            </div>
            <?= GridView::widget([
                'layout' => "{items}\n{summary}\n{pager}",
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    'id',
                    'username',
                    'nickname',
                    'email:email',
                    'mobile',
                    [
                        'header' => Yii::t('yuncms', 'Identification'),
                        'value' => function ($model) {
                            if ($model->identified) {
                                return Yii::t('yuncms', 'Identified');
                            } else {
                                return Yii::t('yuncms', 'Not identified');
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'registration_ip',
                        'value' => function ($model) {
                            return $model->registration_ip == null
                                ? '<span class="not-set">' . Yii::t('yii', '(not set)') . '</span>'
                                : $model->registration_ip;
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'created_at',
                        'format' => 'datetime',
                        'filter' => \yii\jui\DatePicker::widget([
                            'model' => $searchModel,
                            'options' => [
                                'class' => 'form-control'
                            ],
                            'attribute' => 'created_at',
                            'name' => 'created_at',
                            'dateFormat' => 'yyyy-MM-dd'
                        ]),
                    ],
                    [
                        'header' => Yii::t('yuncms', 'Confirmation'),
                        'value' => function ($model) {
                            if ($model->isEmailConfirmed) {
                                return '<div class="text-center"><span class="text-success">' . Yii::t('yuncms', 'Confirmed') . '</span></div>';
                            } else {
                                return Html::a(Yii::t('yuncms', 'Confirm'), ['confirm', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('yuncms', 'Are you sure you want to confirm this user?'),
                                ]);
                            }
                        },
                        'format' => 'raw',
                        'visible' => Yii::$app->settings->get('enableConfirmation', 'user'),
                    ],
                    [
                        'header' => Yii::t('yuncms', 'Block status'),
                        'value' => function ($model) {
                            if ($model->isBlocked) {
                                return Html::a(Yii::t('yuncms', 'Unblock'), ['block', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-success btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('yuncms', 'Are you sure you want to unblock this user?'),
                                ]);
                            } else {
                                return Html::a(Yii::t('yuncms', 'Block'), ['block', 'id' => $model->id], [
                                    'class' => 'btn btn-xs btn-danger btn-block',
                                    'data-method' => 'post',
                                    'data-confirm' => Yii::t('yuncms', 'Are you sure you want to block this user?'),
                                ]);
                            }
                        },
                        'format' => 'raw',
                    ],
                    [
                        'class' => 'yuncms\grid\ActionColumn',
                        'template' => '{assignment} {view} {update} {delete}',
                        'buttons' => ['assignment' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-dashboard"></span>',
                                Url::toRoute(['user-assignment/view', 'id' => $model->id]), [
                                    'title' => Yii::t('yuncms', 'Assignment'),
                                    'aria-label' => Yii::t('yuncms', 'Assignment'),
                                    'data-pjax' => '0',
                                    'class' => 'btn btn-sm btn-default',
                                ]);
                        }]
                    ],
                ]
            ]); ?>
            <?php Box::end(); ?>
        </div>
    </div>
</div>
