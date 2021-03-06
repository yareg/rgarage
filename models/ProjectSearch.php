<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Project;
use yii\helpers\ArrayHelper;

/**
 * ProjectSearch represents the model behind the search form about `app\models\Project`.
 */
class ProjectSearch extends Project
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status_id'], 'integer'],
            [['name'], 'safe'],
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
     * Gets data
     * @param inr $userId
     *
     * @return array
     */
    public function search($userId)
    {
        $query = (new \yii\db\Query())->select(['p.*', 'status_str_id' => 's.str_id', 'status_name' => 's.name',
            'task_id' => 't.id', 'task_description' => 't.description', 'task_deadline' => 't.dt_deadline',
            'task_priority' => 't.priority', 'task_status_id' => 's1.id'])
            ->from(['p' => 'project'])
            ->innerJoin(['s' => 'status'], 's.id = p.status_id')
            ->leftJoin(['t' => 'task'], 't.project_id = p.id')
            ->leftJoin(['s1' => 'status'], 's1.id = t.status_id')
            ->where('user_id = :user_id', ['user_id' => $userId])
            ->andWhere('s.str_id != :status', ['status' => Status::STATUS_DELETED])
            ->andWhere('s1.str_id is null OR s1.str_id != :task_deleted', ['task_deleted' => Status::STATUS_DELETED])
            ->orderBy('p.id, t.priority ASC');

        $projects = $query->all();

        // get only project list
        $queryProject = (new \yii\db\Query())->select(['p.id', 'p.name', 'p.status_id'])
            ->from(['p' => 'project'])
            ->innerJoin(['s' => 'status'], 's.id = p.status_id')
            ->where('user_id = :user_id', ['user_id' => $userId])
            ->andWhere('s.str_id != :status', ['status' => Status::STATUS_DELETED])
            ->orderBy('p.id');
        $projectList = $queryProject->all();

        // group tasks by project
        $result = [];
        foreach ($projects as $project) {
            if (!isset($result[$project['id']])) {
                $result[$project['id']] = [
                    'name' => $project['name'],
                    'status_name' => $project['status_name'],
                    'tasks' => [],
                ];
            }
            $task = [];
            foreach ($project as $field => $value) {
                if (preg_match('/^task_/', $field)) {
                    $task[$field] = $value;
                }
            }
            // check whether loaded empty task record
            if ($task['task_id']) {
                $result[$project['id']]['tasks'][] = $task;
            }
        }
        // add empty projects
        foreach ($projectList as $project) {
            if (!isset($result[$project['id']])) {
                $result[$project['id']] = [
                    'name' => $project['name'],
                    'status_id' => $project['status_id'],
                    'tasks' => [],
                ];
            }
        }

        return $result;
    }
}
