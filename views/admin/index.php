<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Expense;
use app\models\User;

$this->title = 'Expense Review Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-index">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h2 mb-0"><?= Html::encode($this->title) ?></h1>
            <p class="text-muted">Review and manage all expense submissions</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <?= Html::a('<i class="bi bi-arrow-repeat"></i> Refresh', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                <?= Html::a('<i class="bi bi-filter"></i> Filters', '#', ['class' => 'btn btn-outline-primary', 'data-bs-toggle' => 'collapse', 'data-bs-target' => '#filterCollapse']) ?>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Pending Review</h6>
                            <h3 class="card-text"><?= Expense::find()->where(['status' => Expense::STATUS_PENDING])->count() ?></h3>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Approved</h6>
                            <h3 class="card-text"><?= Expense::find()->where(['status' => Expense::STATUS_APPROVED])->count() ?></h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Rejected</h6>
                            <h3 class="card-text"><?= Expense::find()->where(['status' => Expense::STATUS_REJECTED])->count() ?></h3>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="collapse mb-4" id="filterCollapse">
        <div class="card">
            <div class="card-body">
                <?= $this->render('_search', ['model' => $searchModel]) ?>
            </div>
        </div>
    </div>

    <!-- Expenses Grid -->
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">All Expenses</h5>
        </div>
        <div class="card-body p-0">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'layout' => "{items}\n<div class='p-3'>{pager}{summary}</div>",
                'tableOptions' => ['class' => 'table table-hover table-striped mb-0'],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'header' => '#',
                        'headerOptions' => ['style' => 'width: 80px'],
                        'contentOptions' => ['class' => 'fw-bold']
                    ],
                    [
                        'attribute' => 'user_id',
                        'value' => function($model) {
                            return $model->user->username ?? 'Unknown';
                        },
                        'filter' => Html::activeDropDownList(
                            $searchModel, 
                            'user_id', 
                            \yii\helpers\ArrayHelper::map(User::find()->all(), 'id', 'username'),
                            ['class' => 'form-control form-control-sm', 'prompt' => 'All Users']
                        )
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
                            return \yii\helpers\StringHelper::truncateWords($model->description, 10, '...');
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
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'header' => 'Actions',
                        'headerOptions' => ['style' => 'width: 100px'],
                        'contentOptions' => ['class' => 'text-center'],
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('Review', $url, [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                    'title' => 'Review this expense'
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
</div>