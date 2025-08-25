<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'Expense System',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'khzrgqeTu_-5fCoAiizlsmX42gu1FWX5',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'currencyCode' => 'USD', // Replace 'USD' with your desired default currency
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false, // Set to false for better flexibility
            'rules' => [
                // Version 1 API endpoints
                'v1/expense' => 'api/v1/expense/index',
                'v1/expense/<id:\d+>' => 'api/v1/expense/view',
                'v1/expense/create' => 'api/v1/expense/create',
                'v1/expense/update/<id:\d+>' => 'api/v1/expense/update',
                'v1/expense/delete/<id:\d+>' => 'api/v1/expense/delete',
                
                // Legacy API endpoints (backward compatibility)
                'api/expense' => 'api/v1/expense/index',
                'api/expense/<id:\d+>' => 'api/v1/expense/view',
                'api/expense/create' => 'api/v1/expense/create',
                'api/expense/update/<id:\d+>' => 'api/v1/expense/update',
                'api/expense/delete/<id:\d+>' => 'api/v1/expense/delete',
                
                
                // Regular routes
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                
                // Default route
                '' => 'site/index',
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
