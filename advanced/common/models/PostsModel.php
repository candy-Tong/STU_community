<?php

namespace common\models;

use common\models\base\BaseModel;
use frontend\models\QuestionaireForm;
use Yii;

/**
 * This is the model class for table "posts".
 *
 * @property integer $id
 * @property string $label_img
 * @property integer $cat_id
 * @property integer $user_id
 * @property integer $is_valid
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $visable
 */
class PostsModel extends BaseModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_id', 'user_id', 'is_valid', 'created_at', 'updated_at'], 'integer'],
            [['label_img'], 'string', 'max' => 255],
            [['visable'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'label_img' => Yii::t('app', 'Label Img'),
            'cat_id' => Yii::t('app', 'Cat ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'is_valid' => Yii::t('app', 'Is Valid'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'visable' => Yii::t('app', 'Visable'),
        ];
    }

    /**
     * 关联user表
     * @return \yii\db\ActiveQuery
     */
    public function getUser(){
        return $this->hasOne(UserModel::className(),['id'=>'user_id']);
    }

    /**
     * 关联Cats表
     * @return \yii\db\ActiveQuery
     */
    public function getCat(){
        return $this->hasOne(CatsModel::className(),['id'=>'cat_id']);
    }
    /**
     * 关联活动表
     * @return \yii\db\ActiveQuery
     */
    public function getActivity(){
        return $this->hasOne(ActivityModel::className(),['post_id'=>'id']);
    }

    public function getHelp(){
        return $this->hasOne(HelpModel::className(),['post_id'=>'id']);
    }
    public function getContract(){
        return $this->hasMany(ContractModel::className(),['post_id'=>'id']);
    }
    public function getQuestionaire(){
        return $this->hasOne(QuestionaireModel::className(),['post_id'=>'id']);
    }
}
