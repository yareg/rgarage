<?php

namespace app\models;

use Yii;
use yii\base\Exception;
/**
 * This is the model class for table "project".
 *
 * @property integer $id
 * @property string $name
 * @property integer $user_id
 * @property integer $status_id
 *
 * @property Status $status
 * @property User $user
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'user_id', 'status_id'], 'required', 'message' => '{attribute} cannot be blank.'],
            [['user_id', 'status_id'], 'integer'],
            [['name'], 'string', 'max' => 60, 'tooLong' => 'Length cannot be more, then 60 symbols'],
            [['name'], 'string', 'min' => 3, 'tooShort' => 'Length cannot be less, then 3 symbols'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Project name',
            'user_id' => 'User ID',
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * Extend parent function
     *
     * @param array $data
     * @param string $formName
     *
     * @return boolean
    */
    public function load($data, $formName = null)
    {
        // set user ID and default status
        $data['Project']['user_id'] = Yii::$app->user->id;
        $data['Project']['status_id'] = Status::getStatusId(Status::STATUS_ACTIVE);

        return parent::load($data);
    }

    /**
     * Mark project as deleted
    */
    public function delete()
    {
        $statusDeletedId = Status::getStatusId(Status::STATUS_DELETED);
        $this->status_id = $statusDeletedId;
        // update all relative tasks
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            (new \yii\db\Query())->createCommand()
                ->update('task', ['status_id' => $statusDeletedId], ['project_id' => $this->id])
                ->execute();
            $this->save();

            $transaction->commit();
        } catch(Exception $e) {
            $transaction->rollBack();
            throw new \yii\web\BadRequestHttpException();
        }
    }

    /**
     * Check whether project belongs to current user
     *
     * @param int $projectId
     * @return boolean
    */
    public static function belongsToCurrentUser($projectId)
    {
        return (bool) Project::findOne(['id' => $projectId, 'user_id' => Yii::$app->user->id]);
    }
}
