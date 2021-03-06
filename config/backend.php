<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
$config = [
    'bootstrap' => ['yuncms\admin\Bootstrap'],
    'layout' => '@yuncms/admin/views/layouts/main',
    //'defaultRoute' => 'dashboard',
    'controllerMap' => [
        'health' => yuncms\web\controllers\HealthController::class,
        'upload' => yuncms\web\controllers\UploadController::class
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf_backend',
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'authManager' => [
            'class' => yuncms\rbac\DbManager::class,
            'cache' => 'cache',
            'cacheTag' => 'backend.rbac',
            'itemTable' => '{{%admin_auth_item}}',
            'itemChildTable' => '{{%admin_auth_item_child}}',
            'assignmentTable' => '{{%admin_auth_assignment}}',
            'ruleTable' => '{{%admin_auth_rule}}'
        ],
        'userAuthManager' => [
            'class' => yuncms\rbac\DbManager::class,
            'cache' => 'cache',
            'cacheTag' => 'user.rbac',
            'itemTable' => '{{%user_auth_item}}',
            'itemChildTable' => '{{%user_auth_item_child}}',
            'assignmentTable' => '{{%user_auth_assignment}}',
            'ruleTable' => '{{%user_auth_rule}}'
        ],
        'user' => [
            'identityClass' => yuncms\admin\models\Admin::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/admin/security/login'],
            'identityCookie' => [
                'name' => '_identity_backend',
                'httpOnly' => true
            ],
        ],
        'urlManager' => [
            'class' => yii\web\UrlManager::class,
            'rules' => [
                'GET ping' => 'health/ping',
                'login' => '/admin/security/login',
                'logout' => '/admin/security/logout',
            ],
        ],
        'frontUrlManager' => [
            'class' => yii\web\UrlManager::class,
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => yuncms\admin\Module::class
        ]
    ]
];

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/main.php'),
    $config
);