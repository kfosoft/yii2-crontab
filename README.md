# Yii2 Crontab
## Installation

Installation with Composer

Either run
```
composer require kfosoft/yii2-crontab
```

## Configuring
You have to add crontab configuration into `config/console.php`
Example file:
```
<?php

return [
    ...
    'bootstrap'           => [kfosoft\cron\CronManager::COMPONENT_NAME, ...],
    ...
    'components'          => [
        ...
        kfosoft\cron\CronManager::COMPONENT_NAME => [
            'class'     => kfosoft\cron\CronManager::class,
            'tab'       => [
                // Internal job without params
                [
                    'command'    => 'test/command',
                    'type'       => 'internal',
                    'expression' => '*/5 * * * *',
                ],
                // Internal job with params
                [
                    'command'    => 'test/command',
                    'type'       => 'internal',
                    'expression' => '*/1 * * * *',
                    'params'     => [
                        'attribute1' => 'test1',
                        'option1' => 'optTest1',
                    ],
                ],
                // External job example
                [
                    'command'    => '/bin/bash test -a --opt=123',
                    'type'       => 'external',
                    'expression' => '*/1 * * * *',
                ],
            ],
        ],
        ...
    ]
    ...
];
```

## Using
```
bin/yii cron
```

## Supervisor config
```
[program:yii2-cron]
command=/fullpath/to/bin/yii cron 
autostart=true
autorestart=true
user=user_name_or_id
redirect_stderr=true
```