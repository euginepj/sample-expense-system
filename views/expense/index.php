<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Expense;

$this->title = 'My Expenses';
$this->params['breadcrumbs'][] = $this->title;

// Calculate totals
$totalPending = Expense::find()->where(['user_id' => Yii::$app->user->id, 'status' => Expense::STATUS_PENDING])->sum('amount');
$totalApproved = Expense::find()->where(['user_id' => Yii::$app->user->id, 'status' => Expense::STATUS_APPROVED])->sum('amount');
$totalRejected = Expense::find()->where(['user_id' => Yii::$app->user->id, 'status' => Expense::STATUS_REJECTED])->sum('amount');
?>
<div class="expense-index">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted">Manage your expense submissions and track their status</p>
        </div>
        <div class="col-md-4 text-end">
            <?= Html::a('<i class="bi bi-plus-circle"></i> Submit New Expense', ['create'], ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-warning">Pending</h6>
                            <h4 class="card-text"><?= Yii::$app->formatter->asCurrency($totalPending ?: 0) ?></h4>
                            <small class="text-muted">Awaiting review</small>
                        </div>
                        <i class="bi bi-clock-history fs-1 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-success">Approved</h6>
                            <h4 class="card-text"><?= Yii::$app->formatter->asCurrency($totalApproved ?: 0) ?></h4>
                            <small class="text-muted">Ready for reimbursement</small>
                        </div>
                        <i class="bi bi-check-circle fs-1 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-danger">Rejected</h6>
                            <h4 class="card-text"><?= Yii::$app->formatter->asCurrency($totalRejected ?: 0) ?></h4>
                            <small class="text-muted">Needs attention</small>
                        </div>
                        <i class="bi bi-x-circle fs-1 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expenses Grid -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">Expense History</h5>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{items}\n<div class='p-3'>{pager}{summary}</div>",
                'tableOptions' => ['class' => 'table table-hover mb-0'],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'header' => '#',
                        'headerOptions' => ['style' => 'width: 80px'],
                        'contentOptions' => ['class' => 'fw-bold']
                    ],
                    [
                        'attribute' => 'amount',
                        'format' => 'currency',
                        'headerOptions' => ['style' => 'width: 120px'],
                        'contentOptions' => ['class' => 'text-end fw-bold']
                    ],
                    [
                        'attribute' => 'description',
                        'value' => function($model) {
                            return \yii\helpers\StringHelper::truncateWords($model->description, 8, '...');
                        }
                    ],
                    [
                        'attribute' => 'category',
                        'filter' => Expense::getCategories(),
                        'value' => function($model) {
                            return Expense::getCategories()[$model->category] ?? $model->category;
                        },
                        'filterOptions' => ['class' => 'p-1']
                    ],
                    [
                        'attribute' => 'status',
                        'filter' => Expense::getStatuses(),
                        'value' => function($model) {
                            $statuses = Expense::getStatuses();
                            $class = 'status-' . $model->status;
                            return Html::tag('span', $statuses[$model->status] ?? $model->status, [
                                'class' => "badge badge-status $class"
                            ]);
                        },
                        'format' => 'raw',
                        'filterOptions' => ['class' => 'p-1']
                    ],
                    [
                        'attribute' => 'submission_date',
                        'format' => 'datetime',
                        'headerOptions' => ['style' => 'width: 180px'],
                        'filterOptions' => ['class' => 'p-1']
                    ],
                    [
                        'attribute' => 'review_date',
                        'format' => 'datetime',
                        'headerOptions' => ['style' => 'width: 180px'],
                        'visible' => !$searchModel->isNewRecord,
                        'filterOptions' => ['class' => 'p-1']
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'Actions',
                        'headerOptions' => ['style' => 'width: 100px'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('Details', $url, [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'View expense details'
                                ]);
                            }
                        ]
                    ],
                ],
                'pager' => [
                    'options' => ['class' => 'pagination justify-content-center'],
                    'linkContainerOptions' => ['class' => 'page-item'],
                    'linkOptions' => ['class' => 'page-link'],
                ]
            ]); ?>
        </div>
    </div>

    <!-- Quick Tips -->
    <div class="alert alert-info mt-4">
        <h6><i class="bi bi-info-circle"></i> Quick Tips:</h6>
        <ul class="mb-0">
            <li>Expenses typically take 1-2 business days to be reviewed</li>
            <li>Make sure to attach clear receipts for faster approval</li>
            <li>Contact HR if your expense has been pending for more than 3 days</li>
        </ul>
    </div>
</div>