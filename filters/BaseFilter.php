<?php

namespace panix\mod\queue\filters;

use Yii;
use yii\base\Model;
use panix\mod\queue\Env;

/**
 * Class BaseFilter
 *
 */
class BaseFilter extends Model
{
    /**
     * @var Env
     */
    protected $env;

    /**
     * @param Env $env
     * @param array $config
     */
    public function __construct(Env $env, $config = [])
    {
        $this->env = $env;
        parent::__construct($config);
    }

    public static function ensure()
    {
        /** @var static $filter */
        $filter = Yii::createObject(get_called_class());
        $filter->load(Yii::$app->request->queryParams) && $filter->validate();
        $filter->storeParams();
        return $filter;
    }

    /**
     * @return array
     */
    public static function restoreParams()
    {
        return Yii::$app->session->get(get_called_class(), []);
    }

    public function storeParams()
    {
        $params = [];
        foreach ($this->attributes as $attribute => $value) {
            if ($value !== null && $value !== '') {
                $params[$attribute] = $value;
            }
        }
        Yii::$app->session->set(get_called_class(), $params);
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }
}
