<?php
/**
 * @var \yii\web\View $this
 * @var WorkerFilter $filter
 */

use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use panix\mod\queue\filters\WorkerFilter;
use panix\mod\queue\Module;
use panix\mod\queue\records\WorkerRecord;

$this->params['breadcrumbs'][] = Yii::t('queue-monitor/default', 'Workers');

$format = Yii::$app->formatter;
?>
<div class="worker-index">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $filter->search()
                ->active()
                ->with('execTotal')
                ->with('lastExec.push'),
            'sort' => [
                'attributes' => [
                    'host',
                    'pid',
                    'started_at' => [
                        'asc' => ['sender_name' => SORT_ASC, 'id' => SORT_ASC],
                        'desc' => ['sender_name' => SORT_ASC, 'id' => SORT_DESC],
                    ],
                ],
                'defaultOrder' => [
                    'started_at' => SORT_ASC,
                ],
            ],
        ]),
        'emptyText' => Yii::t('queue-monitor/default', 'No workers found.'),
        'tableOptions' => ['class' => 'table table-hover'],
        'formatter' => $format,
        'columns' => [
            'host',
            'pid',
            'started_at:datetime',
            'status:raw',
            'execTotalDone:integer',
            [
                'class' => ActionColumn::class,
                'template' => '{stop}',
                'buttons' => [
                    'stop' => function ($url) {
                        return Html::a(Html::icon('stop'), $url, [
                            'data' => ['method' => 'post', 'confirm' => Yii::t('yii', 'Are you sure?')],
                            'title' => Yii::t('queue-monitor/default', 'Stop the worker.'),
                        ]);
                    },
                ],
                'visibleButtons' => [
                    'stop' => function (WorkerRecord $model) {
                        return Module::getInstance()->canWorkerStop && !$model->isStopped();
                    },
                ],
            ],
        ],
        'rowOptions' => function (WorkerRecord $record) {
            if (!$record->isIdle()) {
                return ['class' => 'active'];
            }
            return [];
        },
        'beforeRow' => function (WorkerRecord $record) use ($format) {
            static $senderName;
            if ($senderName === $record->sender_name) {
                return '';
            }
            $senderName = $record->sender_name;
            $groupTitle = Yii::t('queue-monitor/default', 'Sender: {name} {class}', [
                'name' => $record->sender_name,
                'class' => get_class(Yii::$app->get($record->sender_name)),
            ]);
            return Html::tag('tr', Html::tag('th', $format->asText($groupTitle), ['colspan' => 6]));
        },
    ]) ?>
</div>
