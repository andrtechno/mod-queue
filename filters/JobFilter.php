<?php

namespace panix\mod\queue\filters;

use DateTime;
use Yii;
use panix\mod\queue\Module;
use panix\mod\queue\records\PushQuery;
use panix\mod\queue\records\PushRecord;

/**
 * Class JobFilter
 *
 */
class JobFilter extends BaseFilter
{
    const IS_WAITING = 'waiting';
    const IS_IN_PROGRESS = 'in-progress';
    const IS_DONE = 'done';
    const IS_SUCCESS = 'success';
    const IS_BURIED  = 'buried';
    const IS_FAILED = 'failed';
    const IS_STOPPED = 'stopped';

    public $is;
    public $sender;
    public $class;
    public $pushed_after;
    public $pushed_before;
    public $contains;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['is', 'string'],
            ['is', 'in', 'range' => array_keys($this->scopeList())],
            ['sender', 'string'],
            ['sender', 'trim'],
            ['class', 'string'],
            ['class', 'trim'],
            [['pushed_after', 'pushed_before'], 'string'],
            [['pushed_after', 'pushed_before'], 'validateDatetime'],
            ['contains', 'string'],
            ['contains', 'trim'],
        ];
    }

    public function validateDatetime($attribute)
    {
        if ($this->hasErrors($attribute)) {
            return;
        }
        if ($this->parseDatetime($this->$attribute) === null) {
            $this->addError($attribute, Yii::t('yii', 'The format of {attribute} is invalid.', [
                'attribute' => $this->getAttributeLabel($attribute),
            ]));
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'is' => Yii::t('queue-monitor/main', 'Scope'),
            'sender' => Yii::t('queue-monitor/main', 'Sender'),
            'class' => Yii::t('queue-monitor/main', 'Job'),
            'pushed_after' => Yii::t('queue-monitor/main', 'Pushed After'),
            'pushed_before' => Yii::t('queue-monitor/main', 'Pushed Before'),
            'contains' => Yii::t('queue-monitor/main', 'Contains'),
        ];
    }

    /**
     * @return array
     */
    public function scopeList()
    {
        return [
            self::IS_WAITING => Yii::t('queue-monitor/main', 'Waiting'),
            self::IS_IN_PROGRESS => Yii::t('queue-monitor/main', 'In progress'),
            self::IS_DONE => Yii::t('queue-monitor/main', 'Done'),
            self::IS_SUCCESS => Yii::t('queue-monitor/main', 'Done successfully'),
            self::IS_BURIED => Yii::t('queue-monitor/main', 'Buried'),
            self::IS_FAILED => Yii::t('queue-monitor/main', 'Has failed attempts'),
            self::IS_STOPPED => Yii::t('queue-monitor/main', 'Stopped'),
        ];
    }

    /**
     * @return array
     */
    public function senderList()
    {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('push.sender_name')
                ->groupBy('push.sender_name')
                ->orderBy('push.sender_name')
                ->column();
        }, 3600);
    }

    /**
     * @return array
     */
    public function classList()
    {
        return $this->env->cache->getOrSet(__METHOD__, function () {
            return PushRecord::find()
                ->select('push.job_class')
                ->groupBy('push.job_class')
                ->orderBy('push.job_class')
                ->column();
        }, 3600);
    }

    /**
     * @return PushQuery
     */
    public function search()
    {
        $query = PushRecord::find();
        if ($this->hasErrors()) {
            return $query->andWhere('1 = 0');
        }

        $query->andFilterWhere(['push.sender_name' => $this->sender]);
        $query->andFilterWhere(['like', 'push.job_class', $this->class]);
        $query->andFilterWhere(['like', 'push.job_data', $this->contains]);
        $query->andFilterWhere(['>=', 'push.pushed_at', $this->parseDatetime($this->pushed_after)]);
        $query->andFilterWhere(['<=', 'push.pushed_at', $this->parseDatetime($this->pushed_before, true)]);

        if ($this->is === self::IS_WAITING) {
            $query->waiting();
        } elseif ($this->is === self::IS_IN_PROGRESS) {
            $query->inProgress();
        } elseif ($this->is === self::IS_DONE) {
            $query->done();
        } elseif ($this->is === self::IS_SUCCESS) {
            $query->success();
        } elseif ($this->is === self::IS_BURIED) {
            $query->buried();
        } elseif ($this->is === self::IS_FAILED) {
            $query->hasFails();
        } elseif ($this->is === self::IS_STOPPED) {
            $query->stopped();
        }

        return $query;
    }

    /**
     * @return array
     */
    public function searchClasses()
    {
        return $this->search()
            ->select(['name' => 'push.job_class', 'count' => 'COUNT(*)'])
            ->groupBy(['name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @return array
     */
    public function searchSenders()
    {
        return $this->search()
            ->select(['name' => 'push.sender_name', 'count' => 'COUNT(*)'])
            ->groupBy(['name'])
            ->orderBy(['name' => SORT_ASC])
            ->asArray()
            ->all();
    }

    /**
     * @param string $value
     * @param bool $isEnd
     * @return int|null
     */
    private function parseDatetime($value, $isEnd = false)
    {
        $dt = DateTime::createFromFormat('Y-m-d\TH:i', $value);
        if (!$dt) {
            return null;
        }
        $time = $dt->getTimestamp();
        $time = $time - $time % 60;
        if ($isEnd) {
            $time += 59;
        }
        return $time;
    }
}
