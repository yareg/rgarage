<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $user_name
 * @property string $password_hash
 * @property string $password_salt
 * @property string $first_name
 * @property string $last_name
 * @property integer $dt_created
 *
 * @property Project[] $projects
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function findIdentity($id) {
        $query = User::find()->where('id = :id', ['id' => $id]);
        $user = $query->one();

        return $user;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null) {}

    /**
     * @inheritdoc
     */
    public function getAuthKey() {}

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) {}

    /**
     * @inheritdoc
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'password_hash', 'password_salt', 'dt_created'], 'required'],
            [['dt_created'], 'integer'],
            [['user_name', 'first_name', 'last_name'], 'string', 'max' => 255],
            [['password_hash'], 'string', 'max' => 60],
            [['password_salt'], 'string', 'max' => 22]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'User Name',
            'password_hash' => 'Password Hash',
            'password_salt' => 'Password Salt',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'dt_created' => 'Dt Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProjects()
    {
        return $this->hasMany(Project::className(), ['user_id' => 'id']);
    }

    /**
     * @param string $username
     * @return \app\models\User
    */
    public static function findByUsername($username)
    {
        $query = User::find()->where('user_name = :user_name', ['user_name' => $username]);
        $user = $query->one();

        return $user;
    }

    /**
     * @param string $password
     * @param string $hash
     * @return bool
    */
    public function validatePassword($password, $hash)
    {
        return $hash == crypt($password, $hash);
    }

}
