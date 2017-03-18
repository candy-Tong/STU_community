<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/25
 * Time: 17:07
 */

namespace frontend\models;


use common\models\QuestionModel;
use yii\base\Exception;
use yii\base\Model;

class QuestionForm extends Model
{
    public $id;
    public $question;
    public $questionaire_id;

    const SCENARIO_CREATE='create';
    public function rules()
    {
        return [
            [['id','question','questionaire_id'],'required'],
        ];
    }
    /**
     * 场景
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['id','question','questionaire_id'],
        ];
        return array_merge(parent::scenarios(), $scenarios);
    }

    public function saveQuestion(){
        $this->question=json_decode($this->question);
        foreach ($this->question as $item){
            $model=new QuestionModel();
            $model->question=$item->question;
            $model->questionaire_id=$this->questionaire_id;
            if(!$model->save())
                throw new Exception('fail to save question');
            $choiceModel=new ChoiceForm();
            $choiceModel->choice=$item->choice;
            $choiceModel->question_id=$model->attributes['id'];
            $choiceModel->saveChoice();
        }
    }
}