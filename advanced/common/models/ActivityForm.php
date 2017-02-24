<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/18
 * Time: 14:17
 */

namespace common\models;


use yii\base\Exception;
use yii\base\Model;
use Yii;

class ActivityForm extends Model
{
    public $post_id;
    public $title;
    public $summary;
    public $content;
    public $time;


    public function rules()
    {
        return [
            ['title','require'],
        ];
    }

    public function saveActivity(){
        $model=new ActivityModel();
        $model->post_id=$this->post_id;
        $model->title=Yii::$app->request->post('title');
        $model->content=Yii::$app->request->post('content');
        if(!isset($_POST['summary']))
            $model->summary=$this->_getSummary($model->content);
        else
            $model->summary=Yii::$app->request->post(['summary']);
        $model->time=Yii::$app->request->post('time');
        if(!$model->save())
            throw new Exception('fail to save Activity'.$model->post_id);
    }
    /**
     * 截取事件摘要
     * @param null $content 要截取的内容
     * @param int $begin
     * @param int $end
     * @param string $char
     * @return null|string
     */
    private function _getSummary($content = null, $begin = 0, $end = 90, $char = 'utf8')
    {
        if (empty($content))
            return null;
        return (mb_substr(str_replace('$nbsp;', '', strip_tags($content)), $begin, $end, $char));
    }

    /**
     * 查询活动
     * @param $query
     * @param $curPage
     * @param $pageSize
     * @return mixed
     */
    public function _selectActivity($query,$curPage,$pageSize){
        $data['count']=$query->count();
        $data['data']=$query
            ->with('cat','activity')
            ->offset(($curPage-1)*$pageSize)
            ->limit($pageSize)
            ->asArray()
            ->all();
        $data['data']=self::_formalize($data['data']);
        return $data;
    }

    /**
     * 格式化数据
     * @param $data
     * @return mixed
     */
    public static function _formalize($data){
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