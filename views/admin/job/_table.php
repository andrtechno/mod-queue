<?php
/**
 * @var \yii\web\View $this
 * @var array $values
 */

use yii\helpers\Html;
use yii\helpers\VarDumper;

?>
<?php if (empty($values)): ?>
    <p><?= Yii::t('queue-monitor/default', 'Empty') ?>.</p>
<?php else: ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= Yii::t('queue-monitor/default', 'Name') ?></th>
                <th><?= Yii::t('queue-monitor/default', 'Value') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($values as $name => $value): ?>
            <tr>
                <th><?= Html::encode($name) ?></th>
                <td class="param-value"><?= htmlspecialchars(VarDumper::dumpAsString($value), ENT_QUOTES|ENT_SUBSTITUTE, Yii::$app->charset, true) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
<?php endif ?>
<?php
$this->registerCss(
    <<<CSS
td.param-value {
    word-break: break-all;
}
CSS
);
