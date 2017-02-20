<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/20
 * Time: 18:15
 */

namespace frontend\models;


use common\models\PersonMsgModel;
use yii\base\Model;
use Yii;

class personMsgForm extends Model
{
    public $id;
    public $user_id;
    public $username;
    public $grade;
    public $major;
    public  $avatar;

    public function rules()
    {
        return [
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['grade','integer'],
            ['major','string'],
        ];
    }

    public function updatePersonMsg(){

        $this->user_id=Yii::$app->request->post('user_id');
        $this->username=Yii::$app->request->post('username');
        $this->grade=Yii::$app->request->post('grade');
        $this->major=Yii::$app->request->post('major');
        $this->avatar=Yii::$app->request->post('avatar');

        if ($this->validate())
            return false;

        $model=new PersonMsgModel();
        $model->user_id=$this->user_id;
        $model->username=$this->username;
        $model->grade=$this->grade;
        $model->major=$this->major;
        $model->avatar=$this->avatar;

        if (!$model->save())
            return false;
        return true;
    }

}