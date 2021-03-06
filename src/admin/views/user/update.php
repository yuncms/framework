<?php
use yii\web\View;
use yuncms\helpers\Html;
use yuncms\models\BaseUser;
use yuncms\admin\widgets\Box;
use yuncms\admin\widgets\Toolbar;
use yuncms\admin\widgets\Alert;

/**
 * @var View $this
 * @var BaseUser $model
 * @var string $content
 */

$this->title = Yii::t('yuncms', 'Update User Account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('yuncms', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
if (!isset($this->params['noPadding'])) {
    $this->params['noPadding'] = null;
}

?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <?= Alert::widget() ?>
            <?php Box::begin([
                'noPadding' => $this->params['noPadding'],
                'header' => Html::encode($this->title),
            ]); ?>
            <div class="row">
                <div class="col-sm-6 m-b-xs">
                    <?= Toolbar::widget(['items' => [
                        [
                            'label' => Yii::t('yuncms', 'Manage User'),
                            'url' => ['/admin/user/index'],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Create User'),
                            'url' => ['/admin/user/create'],
                        ],
                        ['label' => Yii::t('yuncms', 'Account details'), 'url' => ['/admin/user/update', 'id' => $model->id]],
                        ['label' => Yii::t('yuncms', 'Profile details'), 'url' => ['/admin/user/update-profile', 'id' => $model->id]],
                        ['label' => Yii::t('yuncms', 'Information'), 'url' => ['/admin/user/view', 'id' => $model->id]],
                        [
                            'label' => Yii::t('yuncms', 'Email Confirm'),
                            'url' => ['/admin/user/confirm', 'id' => $model->id],
                            'visible' => !$model->isEmailConfirmed,
                            'options' => [
                                'class'=>'btn btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yuncms', 'Are you sure you want to confirm this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Mobile Confirm'),
                            'url' => ['/admin/user/confirm', 'id' => $model->id],
                            'visible' => !$model->isMobileConfirmed,
                            'options' => [
                                'class'=>'btn btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yuncms', 'Are you sure you want to confirm this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Block'),
                            'url' => ['/admin/user/block', 'id' => $model->id],
                            'visible' => !$model->isBlocked,
                            'options' => [
                                'class'=>'btn btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yuncms', 'Are you sure you want to block this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Unblock'),
                            'url' => ['/user/user/block', 'id' => $model->id],
                            'visible' => $model->isBlocked,
                            'options' => [
                                'class'=>'btn btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yuncms', 'Are you sure you want to unblock this user?'),
                            ],
                        ],
                        [
                            'label' => Yii::t('yuncms', 'Delete'),
                            'url' => ['/admin/user/delete', 'id' => $model->id],
                            'options' => [
                                'class'=>'btn btn-sm',
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yuncms', 'Are you sure you want to delete this user?'),
                            ],
                        ],
                    ]]); ?>
                </div>
                <div class="col-sm-6 m-b-xs">

                </div>
            </div>
            <?= $content ?>
            <?php Box::end(); ?>
        </div>
    </div>
</div>