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
     *
     * @return array
     */
    public function search()
    {
        $query = (new \yii\db\Query())->select(['p.*', 'status_str_id' => 's.str_id', 'status_name' => 's.name'])
            ->from(['p' => 'project'])
            ->innerJoin(['s' => 'status'], 's.id = p.status_id')
            ->where("s.str_id != :status", ['status' => Status::STATUS_DELETED]);

        $projects = $query->all();
        $projects = ArrayHelper::toArray($projects);

        return $projects;
    }
}
