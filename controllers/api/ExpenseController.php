<?php
namespace app\controllers\api;

use Yii;
use app\models\Expense;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\AccessControl;

class ExpenseController extends ActiveController
{
    public $modelClass = 'app\models\Expense';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        
        // Add CORS support
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => null,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ],
        ];

        // Add authentication
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'],
        ];

        // Add access control
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        if ($user->role === 'admin') {
            $query = Expense::find()->joinWith('user');
        } else {
            $query = Expense::find()->where(['user_id' => $user->id]);
        }

        // Filter by status if provided
        $status = Yii::$app->request->get('status');
        if ($status && in_array($status, [Expense::STATUS_PENDING, Expense::STATUS_APPROVED, Expense::STATUS_REJECTED])) {
            $query->andWhere(['status' => $status]);
        }

        // Filter by category if provided
        $category = Yii::$app->request->get('category');
        if ($category && array_key_exists($category, Expense::getCategories())) {
            $query->andWhere(['category' => $category]);
        }

        return $query->orderBy(['submission_date' => SORT_DESC])->all();
    }

    public function actionView($id)
    {
        $expense = $this->findModel($id);
        $this->checkAccess('view', $expense);
        return $expense;
    }

    public function actionCreate()
    {
        $model = new Expense();
        $model->load(Yii::$app->request->post(), '');
        
        if ($model->save()) {
            Yii::$app->response->setStatusCode(201);
            return $model;
        } else {
            Yii::$app->response->setStatusCode(422);
            return $model->errors;
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess('update', $model);
        
        $model->load(Yii::$app->request->post(), '');
        
        if ($model->save()) {
            return $model;
        } else {
            Yii::$app->response->setStatusCode(422);
            return $model->errors;
        }
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess('delete', $model);
        
        if ($model->delete()) {
            Yii::$app->response->setStatusCode(204);
            return null;
        } else {
            Yii::$app->response->setStatusCode(500);
            return ['error' => 'Failed to delete expense'];
        }
    }

    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested expense does not exist.');
    }

    protected function checkAccess($action, $model = null, $params = [])
    {
        $user = Yii::$app->user->identity;
        
        if ($user->role === 'admin') {
            return true; // Admin can do anything
        }

        if ($model instanceof Expense) {
            // Employees can only access their own expenses
            if ($model->user_id !== $user->id) {
                throw new ForbiddenHttpException('You are not allowed to access this expense');
            }
        }

        return true;
    }
}