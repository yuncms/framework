<?php
use yii\bootstrap\ActiveForm;
use yuncms\helpers\Html;
use yuncms\helpers\ArrayHelper;
use yuncms\helpers\DateHelper;
use yuncms\helpers\ISO3166Helper;

/*
 * @var yii\web\View $this
 * @var yii\bootstrap\ActiveForm $form
 * @var yuncms\user\models\Profile $profile
 */

$this->title = Yii::t('yuncms', 'Profile settings');
$this->params['breadcrumbs'][] = $this->title;


    $items = ISO3166Helper::$countries;
    foreach ($items as $code => $value) {
        $items[$code] = ISO3166Helper::country($code);
    }


// Sort countries list based on user language
$col = new \Collator(Yii::$app->language);
$col->asort($items);



?>

<div class="row">
    <div class="col-md-2">
        <?= $this->render('/_profile_menu') ?>
    </div>
    <div class="col-md-10">
        <h2 class="h3 profile-title"><?= Yii::t('yuncms', 'Profile settings') ?></h2>
        <div class="row">
            <div class="col-md-8">

                <?php $form = ActiveForm::begin([
                    'id' => 'profile-form',
                    'options' => ['class' => 'form-horizontal'],
                    'fieldConfig' => [
                        'template' => "{label}\n<div class=\"col-sm-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-sm-9\">{error}\n{hint}</div>",
                        'labelOptions' => ['class' => 'col-sm-3 control-label'],
                    ],
                    'enableAjaxValidation' => true,
                    'enableClientValidation' => false,
                    'validateOnBlur' => false,
                ]); ?>

                <?= $form->field($model, 'email') ?>

                <?= $form->field($model, 'gender')->inline(true)->radioList(['0' => Yii::t('yuncms', 'Secrecy'), '1' => Yii::t('yuncms', 'Male'), '2' => Yii::t('yuncms', 'Female')], [
                    'template' => "{label}\n<div class=\"col-sm-9\">{input}</div>\n<div class=\"col-sm-offset-3 col-sm-9\">{error}\n{hint}</div>",
                ]); ?>

                <?= $form->field($model, 'country')->dropDownList($items, [
                    'prompt' => Yii::t('yuncms', 'Please select')
                ]); ?>

                <?= $form->field($model, 'location') ?>

                <?= $form->field($model, 'address') ?>

                <?= $form->field($model, 'website') ?>

                <?= $form->field($model, 'timezone')->dropDownList(ArrayHelper::map(DateHelper::getTimeZoneAll(), 'identifier', 'name'), [
                    'prompt' => Yii::t('yuncms', 'Please select')
                ]); ?>

                <?= $form->field($model, 'introduction')->textarea() ?>

                <?= $form->field($model, 'bio')->textarea() ?>

                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <?= Html::submitButton(Yii::t('yuncms', 'Save'), ['class' => 'btn btn-success']) ?>
                        <br>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>