<?php

namespace app\controllers;

use app\models\Rating;
use app\models\Service;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'index', 'about', 'contact'],
                'rules' => [
                    [
                        'actions' => ['logout', 'index', 'about', 'contact'],
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
     * {@inheritdoc}
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
     * @return string
     */
    public function actionIndex()
    {
        $model = new Rating();
        $params = Yii::$app->request->queryParams;
        Yii::warning("queryParams: " . print_r($params, true));
        $excellent_daily_report = count($model->dailyFilter($params, Yii::$app->params['excellent']));
        $good_daily_report = count($model->dailyFilter($params, Yii::$app->params['good']));
        $bad_daily_report = count($model->dailyFilter($params, Yii::$app->params['bad']));

        $current_week_improvement_report = $model->getCurrentImprovementData($params, $model->getCurrentWeekData());
        $last_week_improvement_report = $model->getCurrentImprovementData($params, $model->getLastWeekData());
        Yii::warning("current_week_improvement_report: " . print_r($current_week_improvement_report, true));

        //$excellent_weekly_report = count($model->improvementFilter($params, Yii::$app->params['excellent']));
        //$good_weekly_report = count($model->improvementFilter($params, Yii::$app->params['good']));
        //$bad_weekly_report = count($model->improvementFilter($params, Yii::$app->params['bad']));

      //  $current_month_improvement_report = $model->getCurrentImprovementData($params, $model->getCurrentMonthData());
        //$last_month_improvement_report = $model->getCurrentImprovementData($params, $model->getLastMonthData());
        //Yii::warning("current_monthly_improvement_report: " . print_r($current_month_improvement_report, true));


        $services = ArrayHelper::map(Service::find()->orderBy('name')->all(), 'id', 'name');
        Yii::warning("bad_daily_report: " . print_r($params, true));

        return $this->render('index', [
            'services' => $services,
            'model' => $model,
            'excellent_daily_report' => $excellent_daily_report,
            'good_daily_report' => $good_daily_report,
            'bad_daily_report' => $bad_daily_report,
            'current_week_improvement_report' => $current_week_improvement_report,
            'last_week_improvement_report' => $last_week_improvement_report,
          /*  'excellent_weekly_report'=>$excellent_weekly_report,
            'good_weekly_report'=>$good_weekly_report,
            'bad_weekly_report'=>$bad_weekly_report,
            'last_month_improvement_report'=>$last_month_improvement_report,
            'current_month_improvement_report'=>$last_month_improvement_report,*/
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        $this->layout = "access";
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
