<?php

namespace panix\mod\queue\controllers\admin;

use panix\engine\controllers\AdminController;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use panix\mod\queue\base\FlashTrait;
use panix\mod\queue\Env;
use panix\mod\queue\filters\WorkerFilter;
use panix\mod\queue\Module;
use panix\mod\queue\records\WorkerRecord;

/**
 * DefaultController Controller
 *
 */
class DefaultController extends AdminController
{
    use FlashTrait;

    /**
     * @var Module
     */
    //public $module;
    /**
     * @var Env
     */
    protected $env;

    public function __construct($id, $module, Env $env, array $config = [])
    {
        $this->env = $env;
        parent::__construct($id, $module, $config);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verb' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'stop' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Worker List
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'filter' => WorkerFilter::ensure(),
        ]);
    }

    /**
     * Stops a worker
     *
     * @param int $id
     * @throws ForbiddenHttpException
     * @return \yii\web\Response
     */
    public function actionStop($id)
    {
        if (!$this->module->canWorkerStop) {
            throw new ForbiddenHttpException(Yii::t('queue/notice', 'Stop is forbidden.'));
        }

        $record = $this->findRecord($id);
        $record->stop();
        return $this
            ->success(Yii::t('queue/notice', 'The worker will be stopped within {timeout} sec.', [
                'timeout' => $record->pinged_at + $this->env->workerPingInterval - time(),
            ]))
            ->redirect(['index']);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @return WorkerRecord
     */
    protected function findRecord($id)
    {
        if ($record = WorkerRecord::findOne($id)) {
            return $record;
        }
        throw new NotFoundHttpException(Module::t('notice', 'Record not found.'));
    }
}
