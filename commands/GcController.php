<?php

namespace panix\mod\queue\commands;

use panix\mod\shop\components\ProductPriceHistoryQueue;
use Yii;
use yii\console\Controller;
use panix\mod\queue\records\ExecRecord;
use panix\mod\queue\records\PushRecord;
use panix\mod\queue\records\WorkerRecord;

/**
 * Garbage Collector Commands of Queue Monitor.
 *
 */
class GcController extends Controller
{
    /**
     * @var bool verbose mode.
     */
    public $silent = false;

    /**
     * @inheritdoc
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'silent',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function optionAliases()
    {
        return array_merge(parent::optionAliases(), [
            's' => 'silent',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($this->silent) {
            $this->interactive = false;
        }
        return parent::beforeAction($action);
    }

    /**
     * @inheritdoc
     */
    public function stdout($string)
    {
        if ($this->silent) {
            return false;
        }
        return parent::stdout($string);
    }

    /**
     * Clear deprecated records.
     *
     * @param string $interval
     * @link https://www.php.net/manual/en/dateinterval.construct.php
     */
    public function actionClearDeprecated($interval)
    {
        $ids = PushRecord::find()
            ->deprecated($interval)
            ->done()
            ->select('push.id')
            ->asArray()->column();
        $count = count($ids);
        if ($count && $this->confirm("Do you want to delete $count records?")) {
            $count = PushRecord::getDb()->transaction(function () use ($ids) {
                ExecRecord::deleteAll(['push_id' => $ids]);
                return PushRecord::deleteAll(['id' => $ids]);
            });
            $this->stdout("$count records deleted.\n");
        }
    }

    /**
     * Clear all records.
     */
    public function actionClearAll()
    {
        if ($this->confirm('Are you sure?')) {
            $count = PushRecord::getDb()->transaction(function () {
                WorkerRecord::deleteAll();
                ExecRecord::deleteAll();
                return PushRecord::deleteAll();
            });
            $this->stdout("$count records deleted.\n");
        }
    }

    /**
     * Clear lost worker records.
     */
    public function actionClearWorkers()
    {
        $count = WorkerRecord::deleteAll();
        $this->stdout("$count records deleted.\n");
    }

}
