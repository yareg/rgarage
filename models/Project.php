<?php

namespace app\models;

use Yii;

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
            [['name', 'user_id', 'status_id'], 'required'],
            [['user_id', 'status_id'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
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
}
