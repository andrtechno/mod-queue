<?php
/**
 * @var \yii\web\View $this
 * @var array $values
 */

use yii\helpers\Html;
use yii\helpers\VarDumper;
use panix\mod\queue\Module;

?>
<?php if (empty($values)): ?>
    <p><?= Yii::t('queue-monitor/main', 'Empty') ?>.</p>
<?php else: ?>
    <table class="table table-hover">
        <thead>
            <tr>
                <th><?= Yii::t('queue-monitor/main', 'Name') ?></th>
                <th><?= Yii::t('queue-monitor/main', 'Value') ?></th>
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
