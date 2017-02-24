<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/22
 * Time: 15:49
 */

namespace frontend\models;

use common\models\ActivityForm;
use common\models\ContractModel;
use yii;
use yii\base\Exception;
use common\models\HelpModel;

class HelpForm
{
    public $id;
    public $post_id;
    public $title;
    public $summary;
    public $content;

    const SCENARIO_CREATE='create';


    public function rules()
    {
        return [
            [['id','post_id'], 'required'],
            [['id','post_id'], 'integer'],
        ];
    }

    public function saveHelp(){
        //写入求助帖子表
        $model=new HelpModel();
        $model->scenario=HelpModel::SCENARIO_CREATE;
        $model->post_id=$this->post_id;
        $model->load($_POST,'help');
        if(!isset($_POST['summary']))
            $model->summary=$this->_getSummary($model->content);
        if(!$model->save()){
            throw new Exception('fail to save help');
        }
        //写入联系方式表
        if (!empty($_POST['contract'])){
            //读取post过来的数组的信息，key为type，value为content
            $contract=json_decode($_POST['contract']);

            foreach ($contract as $type=>$content){
                $contractModel=new ContractModel();
                $contractModel->post_id=$this->post_id;
                $contractModel->type=$type;
                $contractModel->content=$content;
                if(!$contractModel->save()){
                    throw new Exception('faile to save contract');
                }
            }
        }
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

    public static function _formalize($data){
        foreach ($data as &$post){
            $post['content']=$post['help']['content'];
            $post['summary']=$post['help']['summary'];
            $post['title']=$post['help']['title'];

            unset($post['help']);
            foreach($post['contract'] as &$contract){
                unset($contract['id']);
                unset($contract['post_id']);
            }
            $post['cat_name']=$post['cat']['cat_name'];
            unset($post['cat']);
        }
        return $data;
    }
}

