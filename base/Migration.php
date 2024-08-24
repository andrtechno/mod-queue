<?php

namespace panix\mod\queue\base;

use panix\mod\queue\Env;

/**
 * Class Migration
 *
 */
abstract class Migration extends \yii\db\Migration
{
    /**
     * @var Env
     */
    protected $env;

    /**
     * @param Env $env
     * @inheritdoc
     */
    public function __construct(Env $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function binary($length = null)
    {
        if ($this->db->driverName === 'mysql') {
            return $this->db->schema->createColumnSchemaBuilder('longblob');
        }
        return parent::binary($length);
    }

    /**
     * @inheritdoc
     */
    public function text()
    {
        if ($this->db->driverName === 'mysql') {
            return $this->db->schema->createColumnSchemaBuilder('longtext');
        }
        return parent::text();
    }
}
