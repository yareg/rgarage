<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "status".
 *
 * @property integer $id
 * @property string $str_id
 * @property string $name
 *
 * @property Task[] $tasks
 */
class Status extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'active';
    const STATUS_DELETED = 'deleted';
    const STATUS_NEW = 'new';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'status';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['str_id', 'name'], 'required'],
            [['str_id', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'str_id' => 'Str ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::className(), ['status_id' => 'id']);
    }

    /**
     * Gets status ID by str_id
     *
     * @param string $strId
     * @return int
    */
    public static function getStatusId($strId)
    {
        return Status::findOne(['str_id' => $strId])->id;
    }

    /**
     * Get array status ID => value by str_id
     * @param array $strIds
     * @return array
    */
    public static function getStatusList(array $strIds)
    {
        $query = Status::find();
        $items = ArrayHelper::toArray($query->all());

        $result = [];
        foreach ($strIds as $strId) {
            foreach($items as $statusItem) {
                if ($statusItem['str_id'] == $strId) {
                    $result[$statusItem['id']] = $statusItem['name'];
                }
            }
        }

        return $result;
    }
}
