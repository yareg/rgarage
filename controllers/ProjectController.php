<?php

namespace app\controllers;

use Yii;
use app\models\Project;
use app\models\ProjectSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
{
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
     * Lists all Project models.
     * @throws \yii\web\NotFoundHttpException()
     * @throws \yii\web\ForbiddenHttpException()
     * @return mixed
     */
    public function actionIndex()
    {
        if (!Yii::$app->request->isAjax) throw new \yii\web\NotFoundHttpException();
//        if (Yii::$app->user->isGuest) throw new \yii\web\ForbiddenHttpException();
        $searchModel = new ProjectSearch();
        $result = $searchModel->search((int) Yii::$app->user->id);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $result;
    }

    /**
     * Displays a single Project model.
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
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @throws \yii\web\NotFoundHttpException
     * @return mixed
     */
    public function actionCreate()
    {
        if (!Yii::$app->request->isAjax) throw new \yii\web\NotFoundHttpException();
        $model = new Project();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = ['status' => 'success'];
        } else {
            $result = ['status' => 'error', 'message' => implode('; ', $model->getErrors())];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $result = ['status' => 'success'];
        } else {
            $result = ['status' => 'error', 'message' => implode('; ', $model->getErrors())];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return $result;
    }

    /**
     * Deletes an existing Project model.
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

        return $this->redirect(['index']);
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
