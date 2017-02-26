<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "question_result".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $question_id
 */
class QuestionResultModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'question_result';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'question_id'], 'required'],
            [['user_id', 'question_id'], 'integer'],
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
            'question_id' => Yii::t('app', 'Question ID'),
        ];
    }
}
