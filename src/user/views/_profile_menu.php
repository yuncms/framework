<?php
use yuncms\widgets\ListGroup;

/** @var yuncms\user\models\User $user */
$user = Yii::$app->user->identity;
$networksVisible = count(Yii::$app->authClientCollection->clients) > 0;

$items = [
    [
        'label' => Yii::t('yuncms', 'Profile Setting'),
        'url' => ['/user/settings/profile'],
        'icon' => 'glyphicon glyphicon-user',
    ],
    [
        'label' => Yii::t('yuncms', 'Security Setting'),
        'url' => ['/user/settings/account'],
        'icon' => 'glyphicon glyphicon-cog'
    ],
    [
        'label' => Yii::t('yuncms', 'Avatar Setting'),
        'url' => ['/user/settings/avatar'],
        'icon' => 'glyphicon glyphicon-picture'
    ],
    [
        'label' => Yii::t('yuncms/identification', 'Identification'),
        'url' => ['/identification/identification/index'],
        'icon' => 'glyphicon glyphicon-education',
        'visible' => Yii::$app->hasModule('identification')
    ],
    [
        'label' => Yii::t('yuncms', 'My Funds'),
        'url' => ['/wallet/wallet/index'],
        'icon' => 'fa fa-money',
        'visible' => Yii::$app->hasModule('wallet')
    ],
    [
        'label' => Yii::t('yuncms', 'My Coin'),
        'url' => ['/coin/coin/index'],
        'icon' => 'fa fa-gift',
        'visible' => Yii::$app->hasModule('coin')
    ],
    [
        'label' => Yii::t('yuncms', 'My Credit'),
        'url' => ['/credit/credit/index'],
        'icon' => 'fa fa-credit-card',
        'visible' => Yii::$app->hasModule('credit')
    ],
    [
        'label' => Yii::t('yuncms', 'OAuth Apps'),
        'url' => ['/oauth2/client/index'],
        'icon' => 'glyphicon glyphicon-paperclip',
        'visible' => Yii::$app->hasModule('oauth2')
    ],
    [
        'label' => Yii::t('yuncms', 'Social Networks'),
        'url' => ['/user/settings/networks'],
        'icon' => 'glyphicon glyphicon-retweet',
        'visible' => $networksVisible
    ],
];
?>

<?= ListGroup::widget([
    'options' => [
        'class' => 'list-group',
    ],
    'encodeLabels' => false,
    'itemOptions' => [
        'class' => 'list-group-item'
    ],
    'items' => $items
]) ?>
