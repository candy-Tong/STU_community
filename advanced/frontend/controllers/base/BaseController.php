<?php
/**
 * Created by PhpStorm.
 * User: candyTong
 * Date: 2017/2/17
 * Time: 15:17
 */

namespace frontend\controllers\base;


use yii\web\Controller;

class BaseController extends Controller
{
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action))
            return false;
        return true;
    }
}