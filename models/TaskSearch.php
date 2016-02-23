<?php

namespace app\models;

use app\controllers\TaskController;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Task;

/**
 * TaskSearch represents the model behind the search form about `app\models\Task`.
 */
class TaskSearch extends Task
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'dt_deadline', 'priority', 'status_id'], 'integer'],
            [['description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param int $projectId
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($projectId, $params)
    {
        $query = (new \yii\db\Query())->select(['t.*'])
            ->from(['t' => 'task'])
            ->innerJoin(['s' => 'status'], 's.id = t.status_id')
            ->where('t.project_id = :project_id', ['project_id' => $projectId])
            ->andWhere('s.str_id != :status_deleted', ['status_deleted' => Status::STATUS_DELETED]);
        $tasks = $query->all();

        return $tasks;
    }

    /**
     * Return priority for new task based on max value existing for project
     * @param int $projectId
     * @return int
    */
    public function searchPriority($projectId)
    {
        $query = (new \yii\db\Query())->select(['new_priority' => '(IFNULL(MAX(priority), 0) + 1)'])
            ->from(['t' => 'task'])
            ->where('t.project_id = :project_id', ['project_id' => $projectId]);

        return $query->one()['new_priority'];
    }

    /**
     * Get neighbor model to be exchanged by priority with already loaded task
     * @param int $projectId
     * @param int $priority
     * @param int $direction
     * @throws NotFoundHttpException
     * @return Task
    */
    public function getModelExchangePriority($projectId, $priority, $direction)
    {
        if (TaskController::DIRECTION_UP == $direction) {
            $condition = '<';
            $order = ' DESC';
        } else if (TaskController::DIRECTION_DOWN == $direction) {
            $condition = '>';
            $order = ' ASC';
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');;
        }
        $model = Task::find()
            ->where(['project_id' => $projectId])
            ->andWhere([$condition, 'priority', $priority])
            ->andWhere('status_id != :deleted', ['deleted' => Status::getStatusId(Status::STATUS_DELETED)])
            ->orderBy('priority' . $order)
            ->limit(1);
         return $model;
    }
}
