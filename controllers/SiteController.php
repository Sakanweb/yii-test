<?php

namespace app\controllers;


use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use app\models\ImportHistory;
use app\models\Import;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */

    const IMPORT_FILE_INFO = [
        "maximum_number_imports" => 2,
        "file_types" => ['csv'],
        "redirect_url" => 'site/imports'
    ];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
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
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
         ];
    }


    /**
     * @return false|string
     */
    public function actionIndex()
    {
        $model = new Import();
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $model->upload();
        }

        return $this->render('index', ['model' => $model, 'import_file_info' => self::IMPORT_FILE_INFO]);
    }

    /**
     * @return string
     */
    public function actionImports() {
        $model = new ImportHistory();
        //$dataProvider = $model->search(\Yii::$app->request->queryParams);
        $dataProvider = new ActiveDataProvider([
            'query' => $model->find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('imports', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }
}
