<?php

use yuncms\helpers\Html;
use yuncms\admin\widgets\Box;
use yuncms\admin\widgets\Toolbar;
use yuncms\admin\widgets\Alert;

/* @var \yii\web\View $this */
/* @var \yuncms\admin\models\AdminMenu $model */

$this->title = Yii::t('yuncms', 'Create Menu');
$this->params['breadcrumbs'][] = ['label' => Yii::t('yuncms', 'Manage Menu'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?= Alert::widget() ?>
            <?php Box::begin([
                'header' => Html::encode($this->title)]); ?>
            <div class="row">
                <div class="col-sm-4 m-b-xs">
                    <?= Toolbar::widget(['items' =>  [
                        [
                            'label' => Yii::t('yuncms', 'Manage Menu'),
                            'url' => ['/admin/menu/index'],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Create Menu'),
                            'url' => ['/admin/menu/create'],
                        ],
                    ]
                    ]); ?>
                </div>
                <div class="col-sm-8 m-b-xs">

                </div>
            </div>
            <?= $this->render('_form', [
                'model' => $model,
            ])
            ?>
            <?php Box::end(); ?>
        </div>
    </div>
</div>