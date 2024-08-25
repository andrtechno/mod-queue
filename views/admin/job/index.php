<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\data\ActiveDataProvider;
use panix\engine\widgets\ListView;
use yii\widgets\Pjax;
use panix\mod\queue\assets\JobItemAsset;
use panix\mod\queue\widgets\FilterBar;


JobItemAsset::register($this);
?>
<div class="monitor-job-index">
    <div class="row">

        <div class="col-lg-9 col-lg-pull-3">

                    <?php Pjax::begin() ?>
                    <?= ListView::widget([
                        'dataProvider' => new ActiveDataProvider([
                            'query' => $filter->search()->with(['parent', 'firstExec', 'lastExec', 'execTotal']),
                            'sort' => [
                                'defaultOrder' => [
                                    'id' => SORT_DESC,
                                ],
                            ],
                        ]),
                        'pager' => ['options' => ['class' => 'pagination ml-auto mr-auto mr-lg-0 ml-lg-auto']],
                        'emptyText' => Yii::t('queue-monitor/default', 'No jobs found.'),
                        'emptyTextOptions' => ['class' => Yii::t('queue-monitor/default', 'empty lead')],
                        'itemView' => '_index-item',
                        'itemOptions' => ['tag' => null],
                    ]) ?>
                    <?php Pjax::end() ?>

        </div>
        <div class="col-lg-3 col-lg-push-9">
            <?php //FilterBar::begin() ?>
            <?= $this->render('_job-filter', compact('filter')) ?>
            <?php //FilterBar::end() ?>
        </div>
    </div>
</div>
