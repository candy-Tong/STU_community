<?php

namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "contract".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $type
 * @property string $content
 */
class ContractModel extends BaseModel
{
    const SCENARIO_CREATE='create';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract';
    }

    /**
     * 场景
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['id','post_id','type','content'],
        ];
        return array_merge(parent::scenarios(), $scenarios);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id'], 'required'],
            [['post_id'], 'integer'],
            [['type', 'content'], 'string', 'max' => 255],
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
            'type' => Yii::t('app', 'Type'),
            'content' => Yii::t('app', 'Content'),
        ];
    }
    public function getPost(){
        return $this->hasOne(PostsModel::className(),['id'=>'post_id']);
    }
}
