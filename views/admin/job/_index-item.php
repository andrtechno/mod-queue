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
        $statusClass = 'info';
        break;
    case PushRecord::STATUS_WAITING:
        $statusClass = 'info';
        break;
    case PushRecord::STATUS_STARTED:
        $statusClass = 'primary';
        break;
    case PushRecord::STATUS_FAILED:
    case PushRecord::STATUS_RESTARTED:
        $statusClass = 'danger';
        break;
    case PushRecord::STATUS_BURIED:
        $statusClass = 'danger';
        break;
    default:
        $statusClass = 'success';
}
?>


<div class="card mb-2">
    <div class="card-body pt-2 pr-2 pb-2 pl-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>
                    <a href="<?= Url::to(['view', 'id' => $model->id]) ?>" data-pjax="0">
                        <?= Yii::t('queue-monitor/default', '#{jobId} by {sender}', [
                            'jobId' => $format->asText($model->job_uid),
                            'sender' => $format->asText($model->sender_name)
                        ]) ?>
                    </a>
                </strong>
                <?php if ($model->parent){ ?>
                    <?= Yii::t('queue-monitor/default', 'from') ?>
                    <a href="<?= Url::to(['view', 'id' => $model->parent->id]) ?>" data-pjax="0">
                        #<?= $format->asText($model->parent->job_uid) ?>
                    </a>
                <?php } ?>
                <small class="ml-4">
                    <?= Yii::t('queue-monitor/default', 'Pushed') ?>: <?= $format->asDatetime($model->pushed_at) ?>
                </small>
                <small class="ml-4" title="<?= Yii::t('main', 'Time to reserve of the job.') ?>">
                    <?= Yii::t('queue-monitor/default', 'TTR') ?>: <?= $format->asInteger($model->ttr) ?>s
                </small>
                <small class="ml-4">
                    <?= Yii::t('queue-monitor/default', 'Delay') ?>: <?= $format->asInteger($model->delay) ?>s
                </small>
                <small class="ml-4" title="<?= Yii::t('queue-monitor/default', 'Number of attempts.') ?>">
                    <?= Yii::t('queue-monitor/default', 'Attempts') ?>
                    : <?= $format->asInteger($model->getAttemptCount()) ?>
                </small>
                <small class="ml-4"
                       title="<?= Yii::t('queue-monitor/default', 'Waiting time from push till first execute.') ?>">
                    <?= Yii::t('queue-monitor/default', 'Wait') ?>: <?= $format->asInteger($model->getWaitTime()) ?>s
                </small>
                <?php if ($model->lastExec) { ?>
                    <small class="ml-4"
                           title="<?= Yii::t('queue-monitor/default', 'Last execute time and memory usage.') ?>">
                        <?= Yii::t('queue-monitor/default', 'Exec') ?>
                        : <?= $format->asInteger($model->lastExec->getDuration()) ?>s
                        <?php if ($model->lastExec->memory_usage) { ?>
                            / <?= $format->asShortSize($model->lastExec->memory_usage, 0) ?>
                        <?php } ?>
                    </small>
                <?php } ?>
            </div>
            <div class="pl-2 pr-2 badge badge-<?= $statusClass ?> text-uppercase"><?= $format->asText($model->getStatusLabel($status)) ?></div>
        </div>
        <h6 class="mt-2">
            <strong><?= $format->asText($model->job_class) ?></strong>
        </h6>
        <div class="job-params">
            <?php foreach ($model->getJobParams() as $property => $value) {
                $val = htmlspecialchars(VarDumper::dumpAsString($value), ENT_QUOTES | ENT_SUBSTITUTE, Yii::$app->charset, true);
                ?>
                <span class="job-param">
                    <span class="job-param-name"><?= $format->asText($property) ?> =</span>
                    <span class="job-param-value"><?= $val ?></span>
                </span>
            <?php } ?>
        </div>
        <?php if ($model->lastExec && $model->lastExec->isFailed()) { ?>
            <div class="job-error text-danger">
                <strong><?= Yii::t('queue-monitor/default', 'Error') ?>:</strong>
                <?= $format->asText($model->lastExec->getErrorMessage()) ?>
            </div>
        <?php } ?>
        <div class="job-border bg-<?= $statusClass ?>"></div>
    </div>
</div>
