<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/25
 * Time: 17:17
 */

namespace frontend\models;


use common\models\ChoiceModel;
use yii\base\Exception;
use yii\base\Model;

class ChoiceForm extends Model
{
    public $id;
    public $question_id;
    public $choice;

    const SCENARIO_CREATE='create';
    public function rules()
    {
        return [
            [['id','question_id','choice'],'required'],
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

    public function saveChoice(){
        if(!is_array($this->choice))
            throw new Exception('chioce is not an array');
        foreach ($this->choice as $oneChoice){
            $model=new ChoiceModel();
            $model->question_id=$this->question_id;
            $model->choice=$oneChoice;
            if(!$model->save())
                throw new Exception('fail to save choice');
        }
    }

}