<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/17
 * Time: 15:17
 */

namespace frontend\controllers;

use common\models\PostForm;
use common\models\PostsModel;
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
            if($model->selectPost())
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
}