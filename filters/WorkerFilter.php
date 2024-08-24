<?php

namespace panix\mod\queue\filters;

use zhuravljov\yii\queue\monitor\records\WorkerQuery;
use zhuravljov\yii\queue\monitor\records\WorkerRecord;

/**
 * Class WorkerFilter
 *
 */
class WorkerFilter extends BaseFilter
{
    /**
     * @return WorkerQuery
     */
    public function search()
    {
        return WorkerRecord::find();
    }
}
