<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/17
 * Time: 15:17
 */

namespace frontend\controllers;

use common\models\PostForm;
use Yii;
use frontend\controllers\base\BaseController;

class PostController extends BaseController
{
    /**
     * 帖子章类型常量
     */
    const ALL=0;
    const ACTIVITY=1;
    const HELP=2;
    const QUESTIONAIRE=3;

    public function init()
    {
        $this->enableCsrfValidation = false;
    }

    public function actionIndex()
    {

        $model=new PostForm();
        //app端请求
        if (Yii::$app->request->post("from") == 'app') {
            //设置页数
            $curPage=!empty(Yii::$app->request->post('curPage'))?Yii::$app->request->post('curPage'):1;
            //设置每页显示帖子条数
            $pageSize=!empty(Yii::$app->request->post('pageSize'))?Yii::$app->request->post('pageSize'):10;
            //设置类型
            $cat=!empty(Yii::$app->request->post('cat'))?Yii::$app->request->post('cat'):null;
            $visibility=!empty($_POST['visibility'])?$_POST['visibility']:0;
            if($model->selectPost($curPage,$pageSize,$cat,$visibility))
                return json_encode(['status'=>'success','data'=>$model->appData]);
            else
                return json_encode(['status'=>'fail']);
        }
        return $this->render('index');
    }


    /**
     * 创建帖子
     */
    public function actionCreate()
    {
        $model = new PostForm();
        //定义场景
        $model->setScenario(PostForm::SCENARIO_CREATE);
        //app端请求
        if (Yii::$app->request->post("from") == 'app') {
            if ($model->createPost())
                return json_encode(['status' => 'success','data'=>$model->appData]);
            return json_encode(['status' => 'fail', 'msg' => $model->_lastError]);
        }
        return json_encode(['status' => 'fail']);
    }

    /**
     * 删除帖子
     * @return string
     */
    public function actionDelete(){
        $model=new PostForm();
        if (Yii::$app->request->post("from") == 'app') {
            if($model->deletePost())
                return json_encode(['status'=>'success']);
            return json_encode(['status'=>'fail','msg'=>$model->_lastError]);
        }
    }

}