<?php
/**
 * @var \yii\web\View $this
 * @var \panix\mod\queue\records\PushRecord $record
 */

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = Yii::t('queue-monitor/default', 'Environment');

$format = Yii::$app->formatter;
?>
<div class="monitor-job-env">
    <h3><?= Yii::t('queue-monitor/default', 'Push Trace') ?></h3>
    <pre><?= $record->trace ?></pre>
    <h3><?= Yii::t('queue-monitor/default', 'Push Context') ?></h3>
    <pre><?= $record->context ?></pre>
</div>
