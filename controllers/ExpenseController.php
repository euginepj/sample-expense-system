<?php
namespace app\controllers;

use Yii;
use app\models\Expense;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile; // Add this import
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ExpenseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new Expense();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Expense();

        if ($model->load(Yii::$app->request->post())) {
            $model->receiptFile = UploadedFile::getInstance($model, 'receiptFile');
            
            // Set user_id before validation since it's required
            $model->user_id = Yii::$app->user->id;
            
            if ($model->validate()) {
                // Upload file if provided
                if ($model->receiptFile) {
                    $model->upload();
                }
                
                if ($model->save(false)) { // Use false to skip re-validation
                    Yii::$app->session->setFlash('success', 'Expense submitted successfully!');
                    return $this->redirect(['index']);
                }
            }
            
            // If we get here, there were validation errors
            Yii::$app->session->setFlash('error', 'Please fix the errors below.');
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested expense does not exist.');
    }
}