<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "activity".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $title
 * @property string $summary
 * @property string $content
 * @property integer $time
 */
class ActivityModel extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'time'], 'required'],
            [['post_id', 'time'], 'integer'],
            [['content'], 'string'],
            [['title', 'summary'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'title' => Yii::t('app', 'Title'),
            'summary' => Yii::t('app', 'Summary'),
            'content' => Yii::t('app', 'Content'),
            'time' => Yii::t('app', 'Time'),
        ];
    }

    public function getPost(){
        return $this->hasOne(PostsModel::className(),['id'=>'post_id']);
    }

}
