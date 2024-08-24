<?php
/**
 * @var \yii\web\View $this
 * @var \panix\mod\queue\records\PushRecord $record
 */

use yii\helpers\Html;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\VarDumper;
use panix\mod\queue\records\ExecRecord;

echo $this->render('_view-nav', ['record' => $record]);

$this->params['breadcrumbs'][] = Yii::t('queue-monitor/main', 'Attempts');

$format = Yii::$app->formatter;
?>
<div class="monitor-job-attempts">
    <?= GridView::widget([
        'dataProvider' => new ActiveDataProvider([
            'query' => $record->getExecs(),
            'sort' => [
                'attributes' => [
                    'attempt',
                ],
                'defaultOrder' => [
                    'attempt' => SORT_DESC,
                ],
            ],
        ]),
        'layout' => "{items}\n{pager}",
        'emptyText' => Yii::t('queue-monitor/main', 'No workers found.'),
        'tableOptions' => ['class' => 'table table-hover'],
        'formatter' => $format,
        'columns' => [
            [
                'attribute' => 'attempt',
                'format' => 'integer',
                'label' => Yii::t('queue-monitor/main', 'Attempt')
            ],
            [
                'attribute' => 'started_at',
                'format' => 'datetime',
                'label' => Yii::t('queue-monitor/main', 'Started')
            ],
            [
                'attribute' => 'finished_at',
                'format' => 'time',
                'label' => Yii::t('queue-monitor/main', 'Finished')
            ],
            [
                'attribute' => 'duration',
                'format' => 'duration',
                'label' => Yii::t('queue-monitor/main', 'Duration')
            ],
            [
                'attribute' => 'memory_usage',
                'format' => 'shortSize',
                'label' => Yii::t('queue-monitor/main', 'Memory Usage')
            ],
            [
                'attribute' => 'retry',
                'format' => 'boolean',
                'label' => Yii::t('queue-monitor/main', 'Is retry?')
            ],
        ],
        'rowOptions' => function (ExecRecord $record) {
            $options = [];
            if ($record->isFailed()) {
                Html::addCssClass($options, 'danger');
            }
            return $options;
        },
        'afterRow' => function (ExecRecord $record) use ($format) {
            if ($record->isFailed()) {
                return strtr('<tr class="error-line danger text-danger"><td colspan="6">{error}</td></tr>', [
                    '{error}' => $format->asNtext($record->error),
                ]);
            }
            if ($result = $record->getResult()) {
                return strtr('<tr class="result-line"><td colspan="6">{result}</td></tr>', [
                    '{result}' => VarDumper::dumpAsString($result),
                ]);
            }
            return '';
        },
    ]) ?>
</div>
<?php
$this->registerCss(
        <<<CSS
tr.result-line > td {
    white-space: normal;
    word-break: break-all;
}
tr.result-line > td {
    white-space: pre;
    word-break: break-all;
}
CSS
);
