<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/17
 * Time: 15:26
 */

namespace common\models;


use frontend\models\HelpForm;
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
            $model->created_at = time();
            $model->updated_at = time();
            if (!$model->save())
                throw new Exception('文章保存失败');

            $data['post_id']=$model->attributes['id'];
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
            default:throw new Exception("the postType doesn't exist");
        }
        $this->on(self::EVENT_AFTER_CREATE, [$this, '_eventselectPost'],$data);
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
     * 事件方法——查找文章
     * @param $event
     * @throws Exception
     */
    public function _eventSelectPost($event){
        switch ($event->data['cat']){
            case 0:null;break;
            case 1:$cat='activity';break;
            case 2:$cat='help';break;
            case 3:$cat='questionaire';break;
            default:throw new Exception('wrong cat');
        }
        $activity=PostsModel::find()->where(['id'=>$event->data['post_id']])->with('cat',$cat)->asArray()->all();
        if (!$activity)
            throw new Exception("fail to select data");
        $activity=$this->_formalize($activity);
        $this->appData=$activity;
    }


    public function selectPost(){
        $model=new PostsModel();
        //设置页数
        $curPage=!empty(Yii::$app->request->post('curPage'))?Yii::$app->request->post('curPage'):1;
        //设置每页显示帖子条数
        $pageSize=!empty(Yii::$app->request->post('pageSize'))?Yii::$app->request->post('pageSize'):10;
        //设置类型
        $cat=!empty(Yii::$app->request->post('cat'))?Yii::$app->request->post('cat'):null;
        //设置条件
        $condition=[
            'cat_id'=>$cat,
            'is_valid'=>10,
        ];
        $select=['created_at','id','label_img','updated_at','user_id'];
        //生成sql语句
        $query=$model->find()->where($condition)->select($select)->with('cat','activity')->orderBy(['id'=>SORT_DESC]);

        $data['count']=$query->count();
        //检查是否超出最末页
        $data['curPage']=(ceil($data['count']/$pageSize)<$curPage)?ceil($data['count'/$pageSize]):$curPage;
        $data['pageSize']=$pageSize;
        $data['start']=($curPage-1)*$pageSize+1;
        $data['end']=(ceil($data['count']/$pageSize==$curPage))?$data['count']:($curPage-1)*$pageSize+$pageSize;
        $data['data']=$query
            ->offset(($curPage-1)*$pageSize)
            ->limit($pageSize)
            ->asArray()
            ->all();
        $data['data']=$this->_formalize($data['data']);
        $this->appData=$data;
        return true;

    }
    private function _formalize($data){
        foreach ($data as &$post){
            $post['content']=$post['activity']['content'];
            $post['summary']=$post['activity']['summary'];
            $post['title']=$post['activity']['title'];
            $post['time']=$post['activity']['time'];
            unset($post['activity']);
            $post['cat_name']=$post['cat']['cat_name'];
            unset($post['cat']);
        }
        return $data;
    }

}