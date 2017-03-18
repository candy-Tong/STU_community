<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "choice".
 *
 * @property integer $id
 * @property integer $question_id
 * @property string $choice
 */
class ChoiceModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'choice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id'], 'required'],
            [['question_id'], 'integer'],
            [['choice'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'question_id' => Yii::t('app', 'Question ID'),
            'choice' => Yii::t('app', 'Choice'),
        ];
    }
    public function getQuestion(){
        return $this->hasOne(QuestionModel::className(),['id'=>'question_id']);
    }
}
