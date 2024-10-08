<?php
/**
 * @var \yii\web\View $this
 * @var \panix\mod\queue\records\PushRecord $record
 */

use yii\data\ActiveDataProvider;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use panix\mod\queue\assets\JobItemAsset;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = Yii::t('queue-monitor/default', 'Details');

JobItemAsset::register($this);
?>
<div class="monitor-job-details">
    <?= DetailView::widget([
        'model' => $record,
        //'formatter' => Module::getInstance()->formatter,
        'attributes' => [
            [
                'attribute' => 'sender_name',
                'format' => 'text',
                'label' => Yii::t('queue-monitor/default', 'Sender'),
            ],
            [
                'attribute' => 'job_uid',
                'format' => 'text',
                'label' => Yii::t('queue-monitor/default', 'Job UID'),
            ],
            [
                'attribute' => 'job_class',
                'format' => 'text',
                'label' => Yii::t('queue-monitor/default', 'Class'),
            ],
            [
                'attribute' => 'ttr',
                'format' => 'integer',
                'label' => Yii::t('queue-monitor/default', 'Push TTR'),
            ],
            [
                'attribute' => 'delay',
                'format' => 'integer',
                'label' => Yii::t('queue-monitor/default', 'Delay'),
            ],
            [
                'attribute' => 'pushed_at',
                'format' => 'relativeTime',
                'label' => Yii::t('queue-monitor/default', 'Pushed'),
            ],
            [
                'attribute' => 'waitTime',
                'format' => 'duration',
                'label' => Yii::t('vmain', 'Wait Time'),
            ],
            [
                'attribute' => 'status',
                'format' => 'text',
                'value' => function ($model) {
                    return $model->getStatusLabel($model->getStatus());
                },
                'label' => Yii::t('queue-monitor/default', 'Status'),
            ],
        ],
        'options' => ['class' => 'table table-hover'],
    ]) ?>

    <?php Pjax::begin() ?>
    <?= ListView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $record->getChildren()
                              ->with(['parent', 'firstExec', 'lastExec', 'execTotal']),
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
        ]),
        'layout' => '<h3>' . Yii::t('queue-monitor/default', 'Sub Jobs') . "</h3>\n{items}\n{pager}",
        'itemView' => '_index-item',
        'itemOptions' => ['tag' => null],
        'emptyText' => false,
    ]) ?>
    <?php Pjax::end() ?>
</div>
