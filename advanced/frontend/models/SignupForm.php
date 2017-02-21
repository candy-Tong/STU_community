<?php
namespace frontend\models;

use common\models\PersonMsgModel;
use yii\base\Exception;
use yii\base\Model;
use common\models\UserModel;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $_lastError;

    const EVENT_AFTER_CREATE=1;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['username', 'unique', 'targetClass' => '\common\models\UserModel', 'message' => 'This account has already been taken.'],



            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\UserModel', 'message' => 'This email address has already been taken.'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return UserModel|null the saved model or null if saving fails
     */
    public function signup()
    {
        //事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!$this->validate()) {
                return null;
            }
            $user = new UserModel();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();

            if (!$user->save())
                throw new Exception('账号保存失败');

            $data['id']=$user->attributes['id'];
            //调用事件
            $this->_evenAfterCreate($data);

            $transaction->commit();

            return $user;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->_lastError = $e->getMessage();
            return null;
        }
    }

    private function _evenAfterCreate($data)
    {

        $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddPersonMsg'],$data);
        //触发事件
        $this->trigger(self::EVENT_AFTER_CREATE);
    }

    public function _eventAddPersonMsg($event){
        $model=new personMsgForm();
        $model->user_id=$event->data['id'];
        if(!$model->updatePersonMsg())
            throw new Exception(''.$model->_lastError);
    }
}
