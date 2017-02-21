<?php

namespace common\models;

use common\models\base\BaseModel;
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
 * @property integer $created_at
 * @property integer $updated_at
 */
class PersonMsgModel extends BaseModel
{

    const SCENARIO_CREATE='create';
    const SCENARIO_UPDATE='update';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person_msg';
    }
    /**
     * 场景
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['id','user_id','username','grade','major','avatar'],
            self::SCENARIO_UPDATE => ['username','grade','avatar','major'],
        ];
        return array_merge(parent::scenarios(), $scenarios);
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'username'], 'required'],
            [['user_id', 'grade', 'created_at', 'updated_at'], 'integer'],
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
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
