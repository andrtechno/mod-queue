<?php

namespace panix\mod\queue\records;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use panix\mod\queue\Env;

/**
 * Worker Record
 *
 * @property int $id
 * @property string $sender_name
 * @property string $host
 * @property int $pid
 * @property int $started_at
 * @property int $pinged_at
 * @property null|int $stopped_at
 * @property null|int $finished_at
 * @property null|int $last_exec_id
 *
 * @property null|ExecRecord $lastExec
 * @property ExecRecord[] $execs
 * @property array $execTotal
 *
 * @property string $status
 * @property int $execTotalStarted
 * @property int $execTotalDone
 * @property int $duration
 *
 */
class WorkerRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     * @return WorkerQuery|object the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(WorkerQuery::class, [get_called_class()]);
    }

    /**
     * @inheritdoc
     */
    public static function getDb()
    {
        return Env::ensure()->db;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Env::ensure()->workerTableName;
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('queue-monitor/default', 'ID'),
            'sender_name' => Yii::t('queue-monitor/default', 'Sender'),
            'host' => Yii::t('queue-monitor/default', 'Host'),
            'pid' => Yii::t('queue-monitor/default', 'PID'),
            'status' => Yii::t('queue-monitor/default', 'Status'),
            'started_at' => Yii::t('queue-monitor/default', 'Started At'),
            'execTotalStarted' => Yii::t('queue-monitor/default', 'Total Started'),
            'execTotalDone' => Yii::t('queue-monitor/default', 'Total Done'),
        ];
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getLastExec()
    {
        return $this->hasOne(ExecRecord::class, ['id' => 'last_exec_id']);
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getExecs()
    {
        return $this->hasMany(ExecRecord::class, ['worker_id' => 'id']);
    }

    /**
     * @return ExecQuery|\yii\db\ActiveQuery
     */
    public function getExecTotal()
    {
        return $this->hasOne(ExecRecord::class, ['worker_id' => 'id'])
            ->select([
                'exec.worker_id',
                'started' => 'COUNT(*)',
                'done' => 'COUNT(exec.finished_at)',
            ])
            ->groupBy('worker_id')
            ->asArray();
    }

    /**
     * @return int
     */
    public function getExecTotalStarted()
    {
        return ArrayHelper::getValue($this->execTotal, 'started', 0);
    }

    /**
     * @return int
     */
    public function getExecTotalDone()
    {
        return ArrayHelper::getValue($this->execTotal, 'done', 0);
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        if ($this->finished_at) {
            return $this->finished_at - $this->started_at;
        }
        return time() - $this->started_at;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        $format = Yii::$app->formatter;
        if (!$this->lastExec) {
            return Yii::t('queue-monitor/default', 'Idle since {time}.', [
                'time' => $format->asRelativeTime($this->started_at),
            ]);
        }
        if ($this->lastExec->finished_at) {
            return Yii::t('queue-monitor/default', 'Idle after a job since {time}.', [
                'time' => $format->asRelativeTime($this->lastExec->finished_at),
            ]);
        }
        return Yii::t('queue-monitor/default', 'Busy since {time}.', [
            'time' => $format->asRelativeTime($this->lastExec->started_at),
        ]);
    }

    /**
     * @return bool
     */
    public function isIdle()
    {
        return !$this->lastExec || $this->lastExec->finished_at;
    }

    /**
     * @return bool marked as stopped
     */
    public function isStopped()
    {
        return !!$this->stopped_at;
    }

    /**
     * Marks as stopped
     */
    public function stop()
    {
        $this->stopped_at = time();
        $this->save(false);
    }
}
