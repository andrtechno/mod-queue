<?php
/**
 * @var \yii\web\View $this
 * @var \panix\mod\queue\records\PushRecord $record
 */

use yii\helpers\Html;
use yii\bootstrap4\Nav;
use panix\mod\queue\filters\JobFilter;
use panix\mod\queue\Module;

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('main', 'Jobs'),
    'url' => ['index'],
];
if ($filtered = JobFilter::restoreParams()) {
    $this->params['breadcrumbs'][] = [
        'label' => Yii::t('main', 'Filtered'),
        'url' => ['index'] + $filtered,
    ];
}
if ($parent = $record->parent) {
    $this->params['breadcrumbs'][]  = [
        'label' => "#$parent->job_uid",
        'url' => [Yii::$app->requestedAction->id, 'id' => $parent->id],
    ];
}
$this->params['breadcrumbs'][]  = [
    'label' => "#$record->job_uid",
    'url' => [Yii::$app->requestedAction->id, 'id' => $record->id],
];

$module = Module::getInstance();
?>
<div class="pull-right">
    <?php if ($module->canExecStop): ?>
        <?= Html::a(Html::icon('stop') . ' ' . Yii::t('queue/main', 'Stop'), ['stop', 'id' => $record->id], [
            'title' => Yii::t('queue/main', 'Mark as stopped.'),
            'data' => [
                'method' => 'post',
                'confirm' => Yii::t('yii', 'Are you sure?'),
            ],
            'disabled' => !$record->canStop(),
            'class' => 'btn btn-' . ($record->canStop() ? 'danger' : 'default'),
        ]) ?>
    <?php endif ?>
    <?php if ($module->canPushAgain): ?>
        <?= Html::a(Html::icon('repeat') . ' ' . Yii::t('queue/main', 'Push Again'), ['push', 'id' => $record->id], [
            'title' => Yii::t('queue/main', 'Push again.'),
            'data' => [
                'method' => 'post',
                'confirm' => Yii::t('yii', 'Are you sure?'),
            ],
            'disabled' => !$record->canPushAgain(),
            'class' => 'btn btn-' . ($record->canPushAgain() ? 'primary' : 'default'),
        ]) ?>
    <?php endif ?>
</div>
<?= Nav::widget([
    'options' => ['class' =>'nav nav-tabs'],
    'items' => [
        [
            'label' => Yii::t('queue/main', 'Details'),
            'url' => ['view-details', 'id' => $record->id],
        ],
        [
            'label' => Yii::t('queue/main', 'Context'),
            'url' => ['view-context', 'id' => $record->id],
        ],
        [
            'label' => Yii::t('queue/main', 'Data'),
            'url' => ['view-data', 'id' => $record->id],
        ],
        [
            'label' => Yii::t('queue/main', 'Attempts ({attempts})', [
                'attempts'=>$record->attemptCount
            ]),
            'url' => ['view-attempts', 'id' => $record->id],
        ],
    ],
]) ?>
