<?php
namespace app\controllers;

use Yii;
use app\models\Expense;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AdminController extends Controller
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
                        'matchCallback' => function ($rule, $action) {
                            // Check if user is admin
                            $user = Yii::$app->user->identity;
                            return $user && $user->role === 'admin';
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'approve' => ['POST'],
                    'reject' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel = new Expense();
        $dataProvider = $searchModel->adminSearch(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        if (Yii::$app->request->isPost) {
            $action = Yii::$app->request->post('action');
            $comment = Yii::$app->request->post('comment', '');
            
            if ($action === 'approve') {
                $model->status = Expense::STATUS_APPROVED;
                $model->reviewed_by = Yii::$app->user->id;
                $model->review_date = new \yii\db\Expression('NOW()');
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Expense approved successfully!');
                }
            } elseif ($action === 'reject') {
                $model->status = Expense::STATUS_REJECTED;
                $model->reviewed_by = Yii::$app->user->id;
                $model->review_date = new \yii\db\Expression('NOW()');
                $model->description .= "\n\nRejection Reason: " . $comment;
                if ($model->save()) {
                    Yii::$app->session->setFlash('warning', 'Expense rejected.');
                }
            }
            
            return $this->redirect(['index']);
        }

        return $this->render('view', [
            'model' => $model,
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