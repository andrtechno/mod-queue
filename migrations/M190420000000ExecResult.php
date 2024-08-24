<?php


use panix\mod\queue\base\Migration;

/**
 * Exec Result
 */
class M190420000000ExecResult extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn($this->env->execTableName, 'result_data', $this->binary()->after('error'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn($this->env->execTableName, 'result_data');
    }
}
