<?php

namespace panix\mod\queue\assets;

use yii\bootstrap4\BootstrapAsset;
use yii\web\AssetBundle;

/**
 * Class JobIndexAsset
 *
 */
class JobItemAsset extends AssetBundle
{
    public $sourcePath = '@vendor/panix/mod-queue/web';
    public $css = [
        'job-item.css',
    ];
    public $depends = [
        BootstrapAsset::class,
    ];
}
