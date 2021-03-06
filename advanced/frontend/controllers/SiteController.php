<?php
namespace frontend\controllers;

use common\models\PersonMsgModel;
use frontend\controllers\base\BaseController;
use frontend\models\personMsgForm;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    public function init(){
        $this->enableCsrfValidation = false;
    }
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
//                        'roles' => ['*'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            if (Yii::$app->request->post("from")=='app')
                return json_encode(['status'=>'success','msg'=>'already login']);
            return $this->goHome();
        }

        $model = new LoginForm();
        //app端请求
        if (Yii::$app->request->post("from")=='app'){
            if ($model->load(Yii::$app->request->post()) && $model->login()) {
                return json_encode(['status'=>'success','user_id'=>$model->user_id]);
            }
            else return json_encode(['status'=>'fail']);
        }
        //other
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        if (Yii::$app->request->post("from")=='app')
            return json_encode(['status'=>'logout success']);
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest){
            if(Yii::$app->request->post('from')=='app')
                return json_encode(['status'=>'fail','msg'=>'already login']);
            else
                $this->goBack();
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                //从app发送过来的请求需要返回json数据
                if (Yii::$app->request->post("from")=='app'){
                    return json_encode(['status'=>'success','user_id'=>$user->attributes['id']]);
                }
                else  if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
                //返回json数据到app
            }else if (Yii::$app->request->post("from")=='app'){
                $error=array_merge($model->getErrors(),['status'=>'fail']);
                return json_encode($error);
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * 创建/更新个人信息
     * @return string
     */
    public function actionUpdatePersonMsg(){
        $model=new personMsgForm();
        if (Yii::$app->request->post('from')=='app'){

            if($model->updatePersonMsg()){
                return json_encode(['status'=>'success']);
            }
            return json_encode(['status'=>'fail','msg'=>$model->_lastError]);
        }
    }

    public function actionSelectPersonMsg(){
        $user_id=Yii::$app->request->post('user_id');
        $data=PersonMsgModel::find()->where(['user_id'=>$user_id])->asArray()->all();
        if(isset($data))
             return json_encode(['status'=>'success','data'=>$data]);
        return json_encode(['status'=>'fail']);
    }

}
