<?php
/**
 * @var \yii\web\View $this
 * @var \panix\mod\queue\records\PushRecord $record
 */

use panix\mod\queue\Module;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = Module::t('main', 'Data');
?>
<div class="monitor-job-data">
    <?= $this->render('_table', [
        'values' => $record->getJobParams(),
    ]) ?>
</div>
