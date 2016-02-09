<?php

namespace app\models;

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
}
