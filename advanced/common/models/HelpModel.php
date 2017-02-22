<?php

namespace common\models;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "help".
 *
 * @property integer $id
 * @property integer $post_id
 * @property string $title
 * @property string $summary
 * @property string $content
 */
class HelpModel extends BaseModel
{
    const SCENARIO_CREATE='create';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'help';
    }

    /**
     * 场景
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['id','post_id','title','summary','content'],
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
        ];
    }
}
