<?php
namespace app\controllers\api\v1;

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

        return $behaviors;
    }

    public function afterAction($action, $result)
    {
        Yii::$app->response->headers->set('X-API-Version', 'v1');
        return parent::afterAction($action, $result);
    }

    public function actions()
    {
        // $actions = parent::actions();
        // unset($actions['view'], $actions['index'], $actions['create'], $actions['update'], $actions['delete']);

        $actions = [];
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

        // Filter by user_id if provided (admin only)
        $user_id = Yii::$app->request->get('user_id');
        if ($user_id && $user->role === 'admin') {
            $query->andWhere(['user_id' => $user_id]);
        }

        return $query->orderBy(['submission_date' => SORT_DESC])->all();
    }

    public function actionView($id)
    {
        $expense = $this->findModel($id);
        $this->checkAccess('view', $expense);
        
        return [
            'data' => $expense,
            'meta' => [
                'version' => 'v1',
                'timestamp' => date('c')
            ]
        ];
    }

    public function actionCreate()
    {
        $model = new Expense();
        $model->load(Yii::$app->request->post(), '');
        
        // Set user_id for the expense
        $model->user_id = Yii::$app->user->id;
        
        if ($model->save()) {
            Yii::$app->response->setStatusCode(201);
            return [
                'data' => $model,
                'meta' => [
                    'version' => 'v1',
                    'status' => 'created'
                ]
            ];
        } else {
            Yii::$app->response->setStatusCode(422);
            return [
                'errors' => $model->errors,
                'meta' => [
                    'version' => 'v1',
                    'status' => 'validation_failed'
                ]
            ];
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $this->checkAccess('update', $model);
        
        $model->load(Yii::$app->request->post(), '');
        
        if ($model->save()) {
            return [
                'data' => $model,
                'meta' => [
                    'version' => 'v1',
                    'status' => 'updated'
                ]
            ];
        } else {
            Yii::$app->response->setStatusCode(422);
            return [
                'errors' => $model->errors,
                'meta' => [
                    'version' => 'v1',
                    'status' => 'validation_failed'
                ]
            ];
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
            return [
                'error' => 'Failed to delete expense',
                'meta' => [
                    'version' => 'v1',
                    'status' => 'error'
                ]
            ];
        }
    }

    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested expense does not exist.');
    }

    public function checkAccess($action, $model = null, $params = [])
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

        // For create action, always allow since user_id will be set to current user
        if ($action === 'create') {
            return true;
        }

        return true;
    }
}