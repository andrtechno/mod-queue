<?php
/**
 * @var \yii\web\View $this
 * @var PushRecord $model
 */

use yii\helpers\Url;
use yii\helpers\VarDumper;
use panix\mod\queue\records\PushRecord;

$format = Yii::$app->formatter;
$status = $model->getStatus();
switch ($status) {
    case PushRecord::STATUS_STOPPED:
        $statusClass = 'bg-info';
        break;
    case PushRecord::STATUS_WAITING:
        $statusClass = 'bg-info';
        break;
    case PushRecord::STATUS_STARTED:
        $statusClass = 'bg-primary';
        break;
    case PushRecord::STATUS_FAILED:
    case PushRecord::STATUS_RESTARTED:
        $statusClass = 'bg-danger';
        break;
    case PushRecord::STATUS_BURIED:
        $statusClass = 'bg-danger';
        break;
    default:
        $statusClass = 'bg-success';
}
?>
<div class="job-item d-none">
    <div class="job-status"><?= $format->asText($model->getStatusLabel($status)) ?></div>
    <div class="job-details">
        <div class="job-push-uid">
            <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" data-pjax="0">
                <?= Yii::t('queue-monitor/default', '#{jobId} by {sender}', [
                    'jobId' => $format->asText($model->job_uid),
                    'sender' => $format->asText($model->sender_name)
                ]) ?>
            </a>
            <?php if ($model->parent): ?>
                <?= Yii::t('queue-monitor/default', 'from') ?>
                <a href="<?= Url::to(['view', 'id' => $model->parent->id]) ?>" data-pjax="0">
                    #<?= $format->asText($model->parent->job_uid) ?>
                </a>
            <?php endif ?>
        </div>
        <div class="job-push-time">
            <?= Yii::t('queue-monitor/default', 'Pushed') ?>: <?= $format->asDatetime($model->pushed_at) ?>
        </div>
        <div class="job-push-ttr" title="<?= Yii::t('main', 'Time to reserve of the job.') ?>">
            <?= Yii::t('queue-monitor/default', 'TTR') ?>: <?= $format->asInteger($model->ttr) ?>s
        </div>
        <div class="job-push-delay">
            <?= Yii::t('queue-monitor/default', 'Delay') ?>: <?= $format->asInteger($model->delay) ?>s
        </div>
        <div class="job-exec-attempts" title="<?= Yii::t('queue-monitor/default', 'Number of attempts.') ?>">
            <?= Yii::t('queue-monitor/default', 'Attempts') ?>: <?= $format->asInteger($model->getAttemptCount()) ?>
        </div>
        <div class="job-exec-wait-time" title="<?= Yii::t('queue-monitor/default', 'Waiting time from push till first execute.') ?>">
            <?= Yii::t('queue-monitor/default', 'Wait') ?>: <?= $format->asInteger($model->getWaitTime()) ?>s
        </div>
        <?php if ($model->lastExec): ?>
            <div class="job-exec-time" title="<?= Yii::t('queue-monitor/default', 'Last execute time and memory usage.') ?>">
                <?= Yii::t('queue-monitor/default', 'Exec') ?>: <?= $format->asInteger($model->lastExec->getDuration()) ?>s
                <?php if ($model->lastExec->memory_usage): ?>
                    / <?= $format->asShortSize($model->lastExec->memory_usage, 0) ?>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
    <div class="job-class">
        <?= $format->asText($model->job_class) ?>
    </div>
    <div class="job-params">
        <?php foreach ($model->getJobParams() as $property => $value): ?>
            <span class="job-param">
            <span class="job-param-name"><?= $format->asText($property) ?> =</span>
            <span class="job-param-value"><?= htmlspecialchars(VarDumper::dumpAsString($value), ENT_QUOTES|ENT_SUBSTITUTE, Yii::$app->charset, true) ?></span>
        </span>
        <?php endforeach ?>
    </div>
    <?php if ($model->lastExec && $model->lastExec->isFailed()): ?>
        <div class="job-error text-danger">
            <strong><?= Yii::t('queue-monitor/default', 'Error') ?>:</strong>
            <?= $format->asText($model->lastExec->getErrorMessage()) ?>
        </div>
    <?php endif ?>
    <div class="job-border <?= $statusClass ?>"></div>
</div>


<div class="card mb-2">
    <div class="card-body pt-3 pr-3 pb-3 pl-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" data-pjax="0">
                    <?= Yii::t('queue-monitor/default', '#{jobId} by {sender}', [
                        'jobId' => $format->asText($model->job_uid),
                        'sender' => $format->asText($model->sender_name)
                    ]) ?>
                </a>
                <?php if ($model->parent): ?>
                    <?= Yii::t('queue-monitor/default', 'from') ?>
                    <a href="<?= Url::to(['view', 'id' => $model->parent->id]) ?>" data-pjax="0">
                        #<?= $format->asText($model->parent->job_uid) ?>
                    </a>
                <?php endif ?>
                <span class="ml-4">
                    <?= Yii::t('queue-monitor/default', 'Pushed') ?>: <?= $format->asDatetime($model->pushed_at) ?>
                </span>
                <span class="ml-4" title="<?= Yii::t('main', 'Time to reserve of the job.') ?>">
                    <?= Yii::t('queue-monitor/default', 'TTR') ?>: <?= $format->asInteger($model->ttr) ?>s
                </span>
                <span class="ml-4">
                    <?= Yii::t('queue-monitor/default', 'Delay') ?>: <?= $format->asInteger($model->delay) ?>s
                </span>
                <span class="ml-4" title="<?= Yii::t('queue-monitor/default', 'Number of attempts.') ?>">
                    <?= Yii::t('queue-monitor/default', 'Attempts') ?>: <?= $format->asInteger($model->getAttemptCount()) ?>
                </span>
                <span class="ml-4" title="<?= Yii::t('queue-monitor/default', 'Waiting time from push till first execute.') ?>">
                    <?= Yii::t('queue-monitor/default', 'Wait') ?>: <?= $format->asInteger($model->getWaitTime()) ?>s
                </span>
                <?php if ($model->lastExec): ?>
                    <span class="ml-4" title="<?= Yii::t('queue-monitor/default', 'Last execute time and memory usage.') ?>">
                        <?= Yii::t('queue-monitor/default', 'Exec') ?>: <?= $format->asInteger($model->lastExec->getDuration()) ?>s
                        <?php if ($model->lastExec->memory_usage): ?>
                            / <?= $format->asShortSize($model->lastExec->memory_usage, 0) ?>
                        <?php endif ?>
                    </span>
                <?php endif ?>
            </div>
            <div class="job-status label pl-2 pr-2 <?= $statusClass ?>"><?= $format->asText($model->getStatusLabel($status)) ?></div>
        </div>


    <div class="job-class">
        <?= $format->asText($model->job_class) ?>
    </div>
    <div class="job-params">
        <?php foreach ($model->getJobParams() as $property => $value): ?>
            <span class="job-param">
            <span class="job-param-name"><?= $format->asText($property) ?> =</span>
            <span class="job-param-value"><?= htmlspecialchars(VarDumper::dumpAsString($value), ENT_QUOTES|ENT_SUBSTITUTE, Yii::$app->charset, true) ?></span>
        </span>
        <?php endforeach ?>
    </div>
    <?php if ($model->lastExec && $model->lastExec->isFailed()): ?>
        <div class="job-error text-danger">
            <strong><?= Yii::t('queue-monitor/default', 'Error') ?>:</strong>
            <?= $format->asText($model->lastExec->getErrorMessage()) ?>
        </div>
    <?php endif ?>
    <div class="job-border <?= $statusClass ?>"></div>
    </div>
</div>
