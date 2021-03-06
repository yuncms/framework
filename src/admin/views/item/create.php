<?php

use yuncms\helpers\Html;
use yuncms\admin\widgets\Box;
use yuncms\admin\widgets\Toolbar;
use yuncms\admin\widgets\Alert;

/* @var \yii\web\View $this */
/* @var \yuncms\admin\models\AdminAuthItem $model */
/* @var \yuncms\admin\ItemController $context */

$labels = $this->context->labels();
if ($labels['Item'] == 'Role') {
    $this->title = Yii::t('yuncms', 'Create Role');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('yuncms', 'Manage Role'), 'url' => ['index']];
    $actions = [
        [
            'label' => Yii::t('yuncms', 'Manage Role'),
            'url' => ['/admin/role/index'],
        ],
        [
            'label' => Yii::t('yuncms', 'Create Role'),
            'url' => ['/admin/role/create'],
        ],
    ];
} else {
    $this->title = Yii::t('yuncms', 'Create Permission');
    $this->params['breadcrumbs'][] = ['label' => Yii::t('yuncms', 'Manage Permission'), 'url' => ['index']];
    $actions = [
        [
            'label' => Yii::t('yuncms', 'Manage Permission'),
            'url' => ['/admin/permission/index'],
        ],
        [
            'label' => Yii::t('yuncms', 'Create Permission'),
            'url' => ['/admin/permission/create'],
        ],
    ];
}
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
                    <?= Toolbar::widget(['items' => $actions]); ?>
                </div>
                <div class="col-sm-8 m-b-xs">

                </div>
            </div>
            <?=
            $this->render('_form', [
                'model' => $model,
            ]);
            ?>
            <?php Box::end(); ?>
        </div>
    </div>
</div>