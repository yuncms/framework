<?php

$migrationPaths = array_merge(
    require(__DIR__ . '/../../migrations.php'),
    [
        //'@yii/caching/migrations',
        //'@yii/log/migrations',
        '@yii/web/migrations',
        '@yii/rbac/migrations',
        '@yii/i18n/migrations',
        '@app/migrations',
    ]
);

return [
    'bootstrap' => [
        'log', 'queue',
    ],
    'components' => [
        'request' => [
            'class' => yuncms\console\Request::class,
        ],
    ],
    'controllerMap' => [
        'task' => [
            'class' => yuncms\console\controllers\TaskController::class,
        ],
        'migrate' => [
            'class' => yuncms\console\controllers\MigrateController::class,
            'templateFile' => '@yuncms/console/views/migrate/migration.php',
            'generatorTemplateFiles'=>[
                'create_table' => '@yuncms/console/views/migrate/createTableMigration.php',
                'drop_table' => '@yuncms/console/views/migrate/dropTableMigration.php',
                'add_column' => '@yuncms/console/views/migrate/addColumnMigration.php',
                'drop_column' => '@yuncms/console/views/migrate/dropColumnMigration.php',
                'create_junction' => '@yuncms/console/views/migrate/createTableMigration.php',
            ],
            'migrationPath' => $migrationPaths,
        ],
    ],
];
