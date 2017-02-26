<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/25
 * Time: 16:47
 */

namespace frontend\models;


use common\models\QuestionaireModel;
use yii\base\Exception;
use yii\base\Model;

class QuestionaireForm extends Model
{
    public $id;
    public $post_id;
    public $title;
    public $summary;

    const SCENARIO_CREATE='create';
    public function rules()
    {
        return [
            [['id','post_id'],'required'],
        ];
    }
    /**
     * 场景
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['id','post_id','title','summary'],
        ];
        return array_merge(parent::scenarios(), $scenarios);
    }

    public function saveQuestionaire(){
        $model=new QuestionaireModel();
        $model->post_id=$this->post_id;
        $model->title=$this->title;
        $model->summary=$this->summary;

        if(!$model->save())
            throw new Exception('fail to save questionaire'.$this->title);

        $questionModel=new QuestionForm();
        $questionModel->questionaire_id=$model->attributes['id'];

        $questionModel->scenario=QuestionaireForm::SCENARIO_CREATE;
        $questionModel->load(\Yii::$app->request->post());
        $questionModel->saveQuestion();
    }


}