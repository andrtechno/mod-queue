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

}
