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
<?php $form = ActiveForm::begin([
    'id' => 'job-filter',
    'method' => 'get',
    'action' => ['/' . Yii::$app->controller->route],
    'enableClientValidation' => false,
]) ?>
<div class="card">
    <div class="card-header">
        <h5>Фильтр</h5>
    </div>
    <div class="card-body">
        <div class="job-filter p-3">
            <div>
                <?= $form->field($filter, 'is')->dropDownList($filter->scopeList(), ['prompt' => '---']) ?>
            </div>
            <div>
                <?= $form->field($filter, 'sender')->dropDownList($filter->senderList(), ['prompt' => '---']) ?>
            </div>
            <div>
                <?= $form->field($filter, 'class')->dropDownList($filter->classList(), ['prompt' => '---']) ?>
            </div>
            <div>
                <?= $form->field($filter, 'contains') ?>
            </div>
            <div>
                <?= $form->field($filter, 'pushed_after')->input('datetime-local', [
                    'placeholder' => 'YYYY-MM-DDTHH:MM',
                ]) ?>
            </div>
            <div>
                <?= $form->field($filter, 'pushed_before')->input('datetime-local', [
                    'placeholder' => 'YYYY-MM-DDTHH:MM',
                ]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary">
            <span class="glyphicon glyphicon-search"></span>
            <?= Yii::t('queue-monitor/main', 'Search') ?>
        </button>
        <?php if (JobFilter::restoreParams()): ?>
            <a href="<?= Url::to(['/' . Yii::$app->controller->route]) ?>" class="btn btn-default">
                <?= Yii::t('queue-monitor/main', 'Reset') ?>
            </a>
        <?php endif ?>
    </div>

</div>
<?php ActiveForm::end() ?>
