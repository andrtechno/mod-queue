<?php

namespace panix\mod\queue\controllers\admin;

use Yii;
use panix\engine\controllers\AdminController;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use panix\mod\queue\base\FlashTrait;
use panix\mod\queue\filters\JobFilter;
use panix\mod\queue\Module;
use panix\mod\queue\records\PushRecord;

/**
 * Class JobController
 *
 */
class JobController extends AdminController
{
    use FlashTrait;

    /**
     * @var Module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'push' => ['post'],
                    'stop' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Pushed jobs
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->pageName = Yii::t('queue-monitor/default', 'Jobs');

        if (JobFilter::restoreParams()) {
            $this->view->params['breadcrumbs'][] = ['label' => $this->pageName, 'url' => ['index']];
            $this->view->params['breadcrumbs'][] = Yii::t('queue-monitor/default', 'Filtered');
        } else {
            $this->view->params['breadcrumbs'][] = $this->pageName;
        }
        return $this->render('index', [
            'filter' => JobFilter::ensure(),
        ]);
    }

    /**
     * Job view
     *
     * @param int $id
     * @return mixed
     */
    public function actionView($id)
    {
        $record = $this->findRecord($id);
        if ($record->lastExec && $record->lastExec->isFailed()) {
            return $this->redirect(['view-attempts', 'id' => $record->id]);
        }
        return $this->redirect(['view-details', 'id' => $record->id]);
    }

    /**
     * Push details
     *
     * @param int $id
     * @return mixed
     */
    public function actionViewDetails($id)
    {
        return $this->render('view-details', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Push environment
     *
     * @param int $id
     * @return mixed
     */
    public function actionViewContext($id)
    {
        return $this->render('view-context', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Job object data
     *
     * @param int $id
     * @return mixed
     */
    public function actionViewData($id)
    {
        return $this->render('view-data', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Attempts
     *
     * @param int $id
     * @return mixed
     */
    public function actionViewAttempts($id)
    {
        return $this->render('view-attempts', [
            'record' => $this->findRecord($id),
        ]);
    }

    /**
     * Pushes a job again
     *
     * @param int $id
     * @throws
     * @return mixed
     */
    public function actionPush($id)
    {
        if (!$this->module->canPushAgain) {
            throw new ForbiddenHttpException(Module::t('notice', 'Push is forbidden.'));
        }

        $record = $this->findRecord($id);

        if (!$record->isSenderValid()) {
            $error = Module::t(
                'notice',
                'The job isn\'t pushed because {sender} component isn\'t found.',
                ['sender'=>$record->sender_name]
            );
            return $this
                ->error($error)
                ->redirect(['view-details', 'id' => $record->id]);
        }

        if (!$record->isJobValid()) {
            $error = Module::t(
                'notice',
                'The job isn\'t pushed because it must be JobInterface instance.'
            );
            return $this
                ->error($error)
                ->redirect(['view-data', 'id' => $record->id]);
        }

        $uid = $record->getSender()->push($record->createJob());
        $newRecord = PushRecord::find()->byJob($record->sender_name, $uid)->one();

        return $this
            ->success(Module::t('notice', 'The job is pushed again.'))
            ->redirect(['view', 'id' => $newRecord->id]);
    }

    /**
     * Stop a job
     *
     * @param int $id
     * @throws
     * @return mixed
     */
    public function actionStop($id)
    {
        if (!$this->module->canExecStop) {
            throw new ForbiddenHttpException(Module::t('notice', 'Stop is forbidden.'));
        }

        $record = $this->findRecord($id);

        if ($record->isStopped()) {
            return $this
                ->error(Module::t('notice', 'The job is already stopped.'))
                ->redirect(['view-details', 'id' => $record->id]);
        }

        if (!$record->canStop()) {
            return $this
                ->error(Module::t('notice', 'The job is already done.'))
                ->redirect(['view-attempts', 'id' => $record->id]);
        }

        $record->stop();

        return $this
            ->success(Module::t('notice', 'The job will be stopped.'))
            ->redirect(['view-details', 'id' => $record->id]);
    }

    /**
     * @param int $id
     * @throws NotFoundHttpException
     * @return PushRecord
     */
    protected function findRecord($id)
    {
        if ($record = PushRecord::find()->byId($id)->one()) {
            return $record;
        }
        throw new NotFoundHttpException(Module::t('notice', 'Record not found.'));
    }
}
