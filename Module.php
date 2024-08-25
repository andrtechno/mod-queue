<?php

namespace panix\mod\queue;

use Yii;
use panix\engine\WebModule;

/**
 * Web Module
 *
 */
class Module extends WebModule
{
    public $icon = '';

    /**
     * @var bool
     */
    public $canPushAgain = false;
    /**
     * @var bool
     */
    public $canExecStop = false;
    /**
     * @var bool
     */
    public $canWorkerStop = false;

    public function getAdminMenu()
    {
        return [
            'modules' => [
                'items' => [
                    [
                        'label' => $this->name,
                        'url' => ['/admin/queue-monitor/job'],
                        'icon' => $this->icon,
                        'visible' => Yii::$app->user->can('/queue-monitor/admin/job/index') || Yii::$app->user->can('/queue-monitor/admin/job/*')
                    ],
                ],
            ],
        ];
    }

    public function getInfo()
    {
        return [
            'label' => Yii::t('queue-monitor/default', 'MODULE_NAME'),
            'author' => 'dev@pixelion.com.ua',
            'version' => '1.0',
            'icon' => $this->icon,
            'description' => Yii::t('queue-monitor/default', 'MODULE_DESC'),
            'url' => ['/admin/queue-monitor'],
        ];
    }
}
