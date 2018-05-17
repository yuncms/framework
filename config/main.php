<?php

return [
    'id' => 'yuncms',
    'name' => 'YUNCMS',
    'sourceLanguage' => 'en-US',
    'bootstrap' => [
        'log', 'queue',
    ],
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
        'i18n' => [
            'class' => yuncms\i18n\I18N::class,
        ],
        'mailer' => [
            'class' => yii\swiftmailer\Mailer::class,
            'viewPath' => '@yuncms/mail',
        ],
        'settings' => [
            'class' => yuncms\components\Settings::class,
            'frontCache' => 'cache'
        ],
        'authManager' => [//前端RBAC
            'class' => yuncms\rbac\DbManager::class,
            'cache' => 'cache',
            'cacheTag' => 'user.rbac',
            'itemTable' => '{{%user_auth_item}}',
            'itemChildTable' => '{{%user_auth_item_child}}',
            'assignmentTable' => '{{%user_auth_assignment}}',
            'ruleTable' => '{{%user_auth_rule}}'
        ],
        'sms' => [
            'class' => yuncms\sms\Sms::class,
            'defaultStrategy' => yuncms\sms\strategies\OrderStrategy::class
        ],
        'path' => [
            'class' => yuncms\services\Path::class,
        ],
        'filesystem' => [
            'class' => yuncms\filesystem\FilesystemManager::class,
            'default' => 'local',
            //'cloud' => 'oss',
            'filesystems' => [
                'local' => [//本地存储
                    'class' => yuncms\filesystem\adapters\Local::class,
                ],
//                'public' => [//公开存储
//                    'class' => yuncms\filesystem\adapters\LocalAdapter::class,
//                ],
//                'cloud' => [//云存储
//                    'class' => yuncms\filesystem\adapters\LocalAdapter::class,
//                ],

                'avatar' => [//头像
                    'class' => yuncms\filesystem\adapters\Local::class,
                    'url' => '@web/avatar'
                ],
                'attachment' => [//附件上传
                    'class' => yuncms\filesystem\adapters\Local::class,
                    'url' => '@web/uploadfiles'
                ],
            ],
        ],
        'notification' => [
            'class' => yuncms\notifications\ChannelManager::class,
            'channels' => [
                'database' => [
                    'class' => yuncms\notifications\channels\DatabaseChannel::class
                ],
//                'mail' => [//默认配置电子邮件渠道
//                    'class' => yuncms\notifications\channels\MailChannel::class
//                ],
            ],
        ],
    ]
];