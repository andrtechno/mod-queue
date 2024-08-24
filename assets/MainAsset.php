<?php

namespace panix\mod\queue\assets;

use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\bootstrap\BootstrapThemeAsset;
use yii\web\AssetBundle;
use yii\web\YiiAsset;

/**
 * Class MainAsset
 *
 */
class MainAsset extends AssetBundle
{
    public $sourcePath = '@vendor/panix/mod-queue/web';
    public $css = [
        'main.css',
    ];
    public $js = [
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        BootstrapThemeAsset::class,
    ];
}
