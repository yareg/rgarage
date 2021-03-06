<?php

namespace app\controllers;

use Yii;
use app\models\Task;
use app\models\Project;
use app\models\TaskSearch;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TaskController implements the CRUD actions for Task model.
 */
class TaskController extends Controller
{
    CONST DIRECTION_UP = 1;
    CONST DIRECTION_DOWN = 2;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Task models.
     * @param int $projectId
     * @return mixed
     */
    public function actionIndex($projectId)
    {
        // TODO: uncomment next line
        // if (!Yii::$app->request->isAjax) throw new \yii\web\NotFoundHttpException();
        $searchModel = new TaskSearch();
        $tasks = $searchModel->search($projectId, Yii::$app->request->queryParams);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $tasks;
    }

    /**
     * Displays a single Task model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Task model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @throws \yii\web\NotFoundHttpException
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->request->isAjax) throw new \yii\web\NotFoundHttpException();
        $model = new Task();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // format task structure
            $result = [
                'status' => 'success',
                'task' => $model->toArray(),
            ];
        } else {
            $result = ['status' => 'error', 'message' => implode('; ', $model->getFirstErrors())];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Updates an existing Task model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = [
                'status' => 'success',
                'task' => $model->toArray(),
            ];
        } else {
            $result = ['status' => 'error', 'message' => implode('; ', $model->getFirstErrors())];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Deletes an existing Task model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->findModel($id)->delete();

            $result= ['status' => 'success'];
        } catch (Exception $e) {
            $result = ['status' => 'error', 'message' => $e->getMessage()];
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Increase task priority
     * @param int $id
     * @return mixed
    */
    public function actionPriorityUp($id)
    {
        return $this->taskPriorityChange($id, self::DIRECTION_UP);
    }

    /**
     * Decrease task priority
     * @param int $id
     * @return mixed
    */
    public function actionPriorityDown($id)
    {
        return $this->taskPriorityChange($id, self::DIRECTION_DOWN);
    }

    /**
     * Change task priority - exchange priority with another
     * @param int $taskId
     * @param int $direction
     * @throws \yii\web\ForbiddenHttpException
     * @return mixed
    */
    private function taskPriorityChange($taskId, $direction)
    {
        // check whether we have permission to modify task
        $currentModel = $this->findModel($taskId);
        if (! Project::belongsToCurrentUser($currentModel->project_id)) {
            throw  new \yii\web\ForbiddenHttpException();
        }
        $exchangeWith = (new TaskSearch())->getModelExchangePriority($currentModel->project_id, $currentModel->priority, $direction);
        // get ID
        $exchangeID = $exchangeWith->asArray()->one()['id'];
        if ($exchangeID) {
            $exchangeModel = $this->findModel($exchangeID);
            // exchange priority values
            $result = Task::exchangePriority($currentModel, $exchangeModel);
        } else {
            $result = ['status' => 'impossible'];
        }

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Finds the Task model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Task the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Task::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
