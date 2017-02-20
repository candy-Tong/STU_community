<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "person_msg".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $username
 * @property integer $grade
 * @property string $major
 * @property string $avatar
 */
class PersonMsgModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person_msg';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'username'], 'required'],
            [['user_id', 'grade'], 'integer'],
            [['username', 'avatar'], 'string', 'max' => 255],
            [['major'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'username' => Yii::t('app', 'Username'),
            'grade' => Yii::t('app', 'Grade'),
            'major' => Yii::t('app', 'Major'),
            'avatar' => Yii::t('app', 'Avatar'),
        ];
    }
}
