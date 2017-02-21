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
    public $_lastError;



    public function rules()
    {
        return [
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['grade','integer'],
            ['major','string'],
        ];
    }


    public function updatePersonMsg(){
        //接受post数据
        if(isset($_POST['user_id']))
            $this->user_id=$_POST['user_id'];
        if(isset($_POST['username']))
            $this->username=$_POST['username'];
        if(isset($_POST['grade']))
            $this->grade=$_POST['grade'];
        if(isset($_POST['major']))
            $this->major=$_POST['major'];
        if(isset($_POST['avatar']))
            $this->avatar=$_POST['avatar'];


        if (!$this->validate()){
            $this->_lastError='validate is not pass';
            return false;
        }
        //检查是否有user_id
        if(isset($_POST['data']['user_id'])) {
            $user_id = $_POST['data']['user_id'];
        }else{
            $this->_lastError='no user_id';
            return false;
        }
        $model=PersonMsgModel::findOne(['user_id'=>$user_id]);
        if(!isset($model)){
            $model=new PersonMsgModel();
            $model->scenario=PersonMsgModel::SCENARIO_CREATE;
            $model->created_at=time();
        }else{
            $model->setScenario(PersonMsgModel::SCENARIO_UPDATE);
        }
        $model->load($_POST,'data');

        $model->updated_at=time();

        if (!$model->save()){

            $this->_lastError=$model->firstErrors;
            return false;
        }
        return true;
    }

}