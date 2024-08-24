<?php

namespace panix\mod\queue\filters;

use panix\mod\queue\records\WorkerQuery;
use panix\mod\queue\records\WorkerRecord;

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
