<?php

return [
    'components' => [
        'formatter' => [
            'class' => yii\i18n\Formatter::class,
            'dateFormat' => 'yyyy-MM-dd',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',
            'timeFormat' => 'HH:mm:ss',
        ],
        'db' => [
            'class' => yii\db\Connection::class,
            'charset' => 'utf8',
            'tablePrefix' => 'yun_',
        ],
        'cache' => [
            'class' => yii\caching\DummyCache::class,
        ],
        'queue' => [
            'class' => yii\queue\sync\Queue::class,
        ],
        'authClientCollection' => [
            'class' => yii\authclient\Collection::class,
        ],
        'settings' => [
            'class' => yuncms\components\Settings::class,
            'frontCache' => 'cache'
        ],
        'filesystem' => [
            'class' => yuncms\filesystem\FilesystemManager::class,
        ],
        'payment' => [
            'class' => yuncms\payment\PaymentManager::class,
        ],
        'notification' => [
            'class' => yuncms\notifications\NotificationManager::class,
        ],
        'sms' => [
            'class' => yuncms\sms\Sms::class,
            'defaultStrategy' => yuncms\sms\strategies\OrderStrategy::class
        ],
        'snowflake' => [
            'class' => yuncms\base\Snowflake::class,
            'workerId' => 0,
            'dataCenterId' => 0,
        ],
    ]
];