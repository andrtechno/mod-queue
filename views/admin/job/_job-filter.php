<?php
/**
 * @var \yii\web\View $this
 * @var JobFilter $filter
 */

use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use panix\mod\queue\filters\JobFilter;
use panix\mod\queue\Module;

?>
<div class="job-filter">
    <?php $form = ActiveForm::begin([
        'id' => 'job-filter',
        'method' => 'get',
        'action' => ['/' . Yii::$app->controller->route],
        'enableClientValidation' => false,
    ]) ?>
    <div class="row">
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'is')->dropDownList($filter->scopeList(), ['prompt' => '']) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'sender')->textInput(['list' => 'job-sender']) ?>
            <?= $this->render('_data-list', ['id' => 'job-sender', 'values' => $filter->senderList()]) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'class')->textInput(['list' => 'job-class']) ?>
            <?= $this->render('_data-list', ['id' => 'job-class', 'values' => $filter->classList()]) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-sm-6">
            <?= $form->field($filter, 'contains') ?>
        </div>
        <div class="col-lg-12 col-md-4 col-xs-6">
            <?= $form->field($filter, 'pushed_after')->input('datetime-local', [
                'placeholder' => 'YYYY-MM-DDTHH:MM',
            ]) ?>
        </div>
        <div class="col-lg-12 col-md-4 col-xs-6">
            <?= $form->field($filter, 'pushed_before')->input('datetime-local', [
                'placeholder' => 'YYYY-MM-DDTHH:MM',
            ]) ?>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">
        <span class="glyphicon glyphicon-search"></span>
        <?= Yii::t('queue-monitor/main', 'Search') ?>
    </button>
    <?php if (JobFilter::restoreParams()): ?>
        <a href="<?= Url::to(['/' . Yii::$app->controller->route]) ?>" class="btn btn-default">
            <?= Yii::t('queue-monitor/main', 'Reset') ?>
        </a>
    <?php endif ?>
    <?php ActiveForm::end() ?>
</div>
