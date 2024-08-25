Yii2 Queue Analytics Module
===========================

The module collects statistics about working of queues of an application, and provides web interface
for research. Also the module allows to stop and replay any jobs manually.

[![Latest Stable Version](https://poser.pugx.org/panix/mod-queue/v/stable)](https://packagist.org/packages/panix/mod-queue)
[![Total Downloads](https://poser.pugx.org/panix/mod-queue/downloads)](https://packagist.org/packages/panix/mod-queue)
[![Monthly Downloads](https://poser.pugx.org/panix/mod-queue/d/monthly)](https://packagist.org/packages/panix/mod-queue)
[![Daily Downloads](https://poser.pugx.org/panix/mod-queue/d/daily)](https://packagist.org/packages/panix/mod-queue)
[![Latest Unstable Version](https://poser.pugx.org/panix/mod-queue/v/unstable)](https://packagist.org/packages/panix/mod-queue)
[![License](https://poser.pugx.org/panix/mod-queue/license)](https://packagist.org/packages/panix/mod-queue)

Installation
------------

The preferred way to install the extension is through [composer](http://getcomposer.org/download/).
Add to the require section of your `composer.json` file:

~~~bash
$ composer require panix/mod-queue
~~~
Usage
-----

To configure the statistics collector, you need to add monitor behavior for each queue component. 
Update common config file:

```php
return [
    'components' => [
        'queue' => [
            // ...
            'as jobMonitor' => \panix\mod\queue\JobMonitor::class,
            'as workerMonitor' => \panix\mod\queue\WorkerMonitor::class,
        ],
    ],
];
```

There are storage options that you can configure by common config file:

```php
return [
    'container' => [
        'singletons' => [
            \panix\mod\queue\Env::class => [
                'cache' => 'cache',
                'db' => 'db',
                'pushTableName'   => '{{%queue_push}}',
                'execTableName'   => '{{%queue_exec}}',
                'workerTableName' => '{{%queue_worker}}',
            ],
        ],
    ],
];
```


And apply migrations.


### Web

Finally, modify your web config file to turn on web interface:

```php
return [
    'bootstrap' => [
        'monitor',
    ],
    'modules' => [
        'monitor' => [
            'class' => \panix\mod\queue\Module::class,
        ],
    ],
];
```

It will be available by URL `http://yourhost.com/monitor`.


### Console

There is console garbage collector:

```php
'controllerMap' => [
    'monitor' => [
        'class' => \panix\mod\queue\console\GcController::class,
    ],
],
```

It can be executed as:

```sh
php yii monitor/clear-deprecated P1D
```

Where `P1D` is [interval spec] that specifies to delete all records one day older.

[interval spec]: https://www.php.net/manual/en/dateinterval.construct.php
