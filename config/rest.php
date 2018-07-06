<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */

$config = [
    'controllerMap' => [
        'auth' => yuncms\rest\controllers\AuthController::class,
        'person' => yuncms\rest\controllers\PersonController::class,
        'sms' => yuncms\rest\controllers\SmsController::class,
        'uploader' => yuncms\rest\controllers\UploaderController::class,
        'health' => yuncms\web\controllers\HealthController::class,
        'notification' => yuncms\rest\controllers\NotificationController::class,
    ],
    'components' => [
        'cache' => [
            'keyPrefix' => 'rest',//前缀
        ],
        'request' => [
            'class' => yuncms\web\Request::class,
            'enableCookieValidation' => false,
            'enableCsrfCookie' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'multipart/form-data' => yii\web\MultipartFormDataParser::class,
                'application/json' => yii\web\JsonParser::class,
                'text/json' => yii\web\JsonParser::class,
                'application/xml' => yuncms\web\XmlParser::class,
                'text/xml' => yuncms\web\XmlParser::class
            ],
            'secureProtocolHeaders' => [
                'X-Forwarded-Proto' => ['https'], // Common
                'Front-End-Https' => ['on'], // Microsoft
                'X-Client-Scheme' => ['https'],// Aliyun CDN
                'X-Client-Proto' => ['https'],
            ],
            'ipHeaders' => [
                'X-Forwarded-For',// Common
                'ALI-CDN-REAL-IP',// Aliyun CDN
                'X-Cluster-Client-IP',
            ],
        ],
        'response' => [
            'class' => yuncms\web\Response::class,
        ],
        'urlManager' => [
            'class' => yii\web\UrlManager::class,
            'rules' => [
                'GET ping' => 'health/ping',
                [
                    'class' => yii\rest\UrlRule::class,
                    'controller' => 'notification',
                    'tokens' => ['{id}' => '<id:[\w+]+>'],
                    'except' => ['delete', 'create', 'update'],
                    'extraPatterns' => [
                        'POST mark-read' => 'mark-read',
                        'POST batch-mark-read' => 'batch-mark-read',
                    ],
                ],
            ],
        ],
    ],
];

return yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/main.php'),
    $config
);