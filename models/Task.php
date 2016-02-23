<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "task".
 *
 * @property integer $id
 * @property integer $project_id
 * @property string $description
 * @property integer $dt_deadline
 * @property integer $priority
 * @property integer $status_id
 *
 * @property Status $status
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['project_id', 'description', 'status_id'], 'required'],
            [['project_id', 'dt_deadline', 'priority', 'status_id'], 'integer'],
            [['description'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'description' => 'Description',
            'dt_deadline' => 'Dt Deadline',
            'priority' => 'Priority',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(Status::className(), ['id' => 'status_id']);
    }

    /**
     * Extend parent function
     *
     * @param array $data
     * @param string $formName
     * @throws \yii\web\ForbiddenHttpException()
     *
     * @return boolean
     */
    public function load($data, $formName = null)
    {
        // get project ID - depends on new record or updated
        $projectId = isset($data['Task']['project_id']) ? $data['Task']['project_id'] : $this->project_id;
        if (! Project::belongsToCurrentUser($projectId)) {
            throw  new \yii\web\ForbiddenHttpException();
        }
        // set default status to new task if empty - means new task is creating
        if (empty($data['Task']['status_id'] )) {
            $data['Task']['status_id'] = Status::getStatusId(Status::STATUS_NEW);
            // get appropriate "priority" value
            $data['Task']['priority'] = (new TaskSearch())->searchPriority($projectId);
        }
        return parent::load($data);
    }

    /**
     * Mark task as deleted
     *
     * @throws \yii\web\ForbiddenHttpException()
     */
    public function delete()
    {
        // check permissions
        if (! Project::belongsToCurrentUser($this->project_id)) {
            throw new \yii\web\ForbiddenHttpException();
        }
        $this->status_id = Status::getStatusId(Status::STATUS_DELETED);
        $this->save();
    }

    /**
     * Exchange priority field values
     * @param Task $currentModel
     * @param Task $exchangeModel
     * @throws ErrorException
     * @return array
    */
    public static function exchangePriority(Task $currentModel, Task $exchangeModel)
    {
        $db = $currentModel->getDb();
        $db->beginTransaction();
        try {
            $currentTmp = $currentModel->priority;
            $exchangeTmp = $exchangeModel->priority;
            $currentModel->priority = 0;
            $currentModel->save();
            $exchangeModel->priority = $currentTmp;
            $exchangeModel->save();
            $currentModel->priority = $exchangeTmp;
            $currentModel->save();

            $db->getTransaction()->commit();
        } catch (Exception $e) {
            $db->getTransaction()->rollBack();
            throw new ErrorException($e->getMessage());
        }

        return ['status' => 'success'];
    }
}
