<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/17
 * Time: 15:26
 */

namespace common\models;


use frontend\models\HelpForm;
use frontend\models\QuestionaireForm;
use yii\base\Exception;
use yii\base\Model;
use common\models\PostsModel;
use Yii;

class PostForm extends Model
{
    public $id;
    public $label_img;
    public $cat_id;

    public $_lastError = "";
    //用于返回数据
    public $appData;

    /**
     * 定义帖子类型
     */
    const ACTIVITY="1";
    const HELP='2';
    const QUESTIONAIRE="3";

    /**
     * 定义事件
     */
    const EVENT_AFTER_CREATE = 'afterCreate';
    /**
     * 场景定义
     * 创建、更新
     */
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * 场景
     */
    public function scenarios()
    {
        $scenarios = [
            self::SCENARIO_CREATE => ['label_img', 'cat_id'],
            self::SCENARIO_UPDATE => ['label_img', 'cat_id'],
        ];
        return array_merge(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        return [
            [['id'], 'required'],
            [['id', 'cat_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'label_img' => '标签图',
        ];
    }

    /**
     * 创建帖子
     * @return bool
     */
    public function createPost()
    {
        //事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $model = new PostsModel();

            $model->cat_id=Yii::$app->request->post("cat");
            $model->user_id = Yii::$app->request->post("user_id");
            if(!isset($_POST['visableToAll'])||$_POST['visableToAll']==0)
                $model->visable=null;
            else{
                $userModel=PersonMsgModel::findOne(['user_id'=>$model->user_id]);
                if (!isset($userModel))
                    throw new Exception('the user is not exist');
                $model->visable=$userModel->major;
            }
            $model->created_at = time();
            $model->updated_at = time();
            if (!$model->save())
                throw new Exception('文章保存失败');

            $data['post_id']=$model->attributes['id'];
            $this->appData=['user_id'=>$model->attributes['id']];
            $data['cat']=Yii::$app->request->post("cat");


            //调用事件
            $this->_evenAfterCreate($data);


            $transaction->commit();


            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->_lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * 事件调用
     */
    private function _evenAfterCreate($data)
    {
        //添加事件
        //新增指定类型的帖子
        //关联查询新增数据
        switch ($data['cat']){
            case self::ACTIVITY:
                $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddActivity'],$data);
                break;
            case self::HELP:
                $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddHelp'],$data);
                break;
            case self::QUESTIONAIRE:
                $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventAddQuestionaire'],$data);
                break;
            default:throw new Exception("the postType doesn't exist");
        }
//        $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventselectPost'],$data);
        //触发事件
        $this->trigger(self::EVENT_AFTER_CREATE);
    }

    /**
     * 事件方法——新增活动
     * @param $event
     */
    public function _eventAddActivity($event)
    {
        $model=new ActivityForm();
        $model->post_id=$event->data['post_id'];
        $model->saveActivity();
    }

    /**
     * 事件方法——新增文章
     * @param $event
     */
    public function _eventAddHelp($event){
        $model=new HelpForm();
        $model->post_id=$event->data['post_id'];
        $model->saveHelp();
    }

    /**
     * 事件方法——新增问卷
     * @param $event
     */
    public function _eventAddQuestionaire($event){
        $model=new QuestionaireForm();
        $model->post_id=$event->data['post_id'];
        $model->scenario=QuestionaireForm::SCENARIO_CREATE;
        $model->load(Yii::$app->request->post());
        $model->saveQuestionaire();
    }


    public function selectPost($curPage,$pageSize,$cat){
        $model=new PostsModel();

        //设置条件
        $condition=[
            'cat_id'=>$cat,
            'is_valid'=>10,     //10为有效文章
        ];
        $select=['created_at','id','label_img','updated_at','user_id','cat_id'];
        //生成sql语句
        $query=$model->find()->where($condition)->select($select)->orderBy(['id'=>SORT_DESC]);

        $data['count']=$query->count();
        switch ($cat){
            case self::ACTIVITY:
                $query=$query->with('cat','activity');
                break;
            case self::HELP:
                $query=$query->with('cat','help','contract');
                break;
            case self::QUESTIONAIRE:
                $query=$query->with('cat','questionaire');
            default:
        }
        //检查curPage是否合法
        $curPage=(ceil($data['count']/$pageSize)<$curPage)?ceil($data['count']/$pageSize):$curPage;
        $data['data']=$query
            ->offset(($curPage-1)*$pageSize)
            ->limit($pageSize)
            ->asArray()
            ->all();
        switch ($cat){
            case self::ACTIVITY:
                $data['data']=ActivityForm::_formalize($data['data']);
                break;
            case self::HELP:
                $data['data']=HelpForm::_formalize($data['data']);
                break;
            case self::QUESTIONAIRE:
                $cat_name='questionaire';
                break;
            default:
        }
        //检查是否超出最末页
        $data['curPage']=$curPage;
        $data['pageSize']=$pageSize;
        $data['start']=($curPage-1)*$pageSize+1;
        $data['end']=(ceil($data['count']/$pageSize)==$curPage)?$data['count']:($curPage-1)*$pageSize+$pageSize;

        $this->appData=$data;
        return true;

    }

    public function deletePost(){
        //事务
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $id=$_POST['post_id'];
            if (!isset($id)){
                throw new Exception('no post_id');
            }
            $model=PostsModel::findOne($id);
            if(!isset($model))
                throw new Exception('can\'t find the post');
            $cat=$model->attributes['cat_id'];
            if($model->delete()==false)
                throw new Exception("fail to delete");
            switch ($cat){
                case self::ACTIVITY:
                    $activityModel=ActivityModel::findOne(['post_id'=>$id]);
                    if (!isset($activityModel))
                        throw new Exception('can\'t find the activity');
                    if ($activityModel->delete()==false)
                        throw new Exception('fail to delete activity');

                    break;
                case self::HELP:
                    $helpModel=HelpModel::findOne(['post_id'=>$id]);
                    if(!$helpModel->delete())
                        throw new Exception('fail to delete help');
                    $contractModel=ContractModel::findAll(['post_id'=>$id]);
                    foreach ($contractModel as $item){
                        if($item->delete()==false)
                            throw new Exception('fail to delete contract');
                    }
                    break;
                case self::QUESTIONAIRE:
                    break;
                default:throw new Exception('can\'t find the cat');
            }


            $transaction->commit();


            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->_lastError = $e->getMessage();
            return false;
        }
    }

}