<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\data\ActiveDataProvider;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use panix\mod\queue\assets\JobItemAsset;
use panix\mod\queue\widgets\FilterBar;


JobItemAsset::register($this);
?>
<div class="monitor-job-index">
    <div class="row">

        <div class="col-lg-9 col-lg-pull-3">
            <div class="card">
                <div class="card-header">
                    <h5><?= Yii::t('queue-monitor/main', 'Jobs'); ?></h5>
                </div>
                <div class="card-body">
                    <?php Pjax::begin() ?>
                    <?= ListView::widget([
                        'dataProvider' => new ActiveDataProvider([
                            'query' => $filter->search()
                                ->with(['parent', 'firstExec', 'lastExec', 'execTotal']),
                            'sort' => [
                                'defaultOrder' => [
                                    'id' => SORT_DESC,
                                ],
                            ],
                        ]),
                        'emptyText' => Yii::t('queue-monitor/main', 'No jobs found.'),
                        'emptyTextOptions' => ['class' => Yii::t('queue-monitor/main', 'empty lead')],
                        'itemView' => '_index-item',
                        'itemOptions' => ['tag' => null],
                    ]) ?>
                    <?php Pjax::end() ?>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-lg-push-9">
            <?php FilterBar::begin() ?>
            <?= $this->render('_job-filter', compact('filter')) ?>
            <?php FilterBar::end() ?>
        </div>
    </div>
</div>
