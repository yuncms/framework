<?php

use yii\helpers\Html;
use yii\grid\GridView;
use xutl\inspinia\Box;
use xutl\inspinia\Toolbar;
use xutl\inspinia\Alert;

/* @var \yii\web\View $this */
/* @var \yuncms\admin\models\AdminBizRule $model */
/* @var \yii\data\ActiveDataProvider $dataProvider */
/* @var \yuncms\admin\models\AdminBizRuleSearch $searchModel */

$this->title = Yii::t('yuncms', 'Manage Rule');
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
                            'label' => Yii::t('yuncms', 'Manage Rule'),
                            'url' => ['/admin/rule/index'],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Create Rule'),
                            'url' => ['/admin/rule/create'],
                        ],
                    ]
                    ]); ?>
                </div>
                <div class="col-sm-8 m-b-xs">

                </div>
            </div>
            <?= GridView::widget([
                'options' => ['id' => 'gridview'],
                'layout' => "{items}\n{summary}\n{pager}",
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('yuncms', 'Rule Name'),
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'header' => Yii::t('yuncms', 'Operation')],
                ],
            ]);
            ?>
            <?php Box::end(); ?>
        </div>
    </div>
</div>
