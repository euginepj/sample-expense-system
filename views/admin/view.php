<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
use app\models\Expense;

$this->title = 'Review Expense #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Expense Review', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admin-view">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Expense Details</h5>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'user_id',
                                'value' => function($model) {
                                    return $model->user->username ?? 'Unknown User';
                                },
                                'format' => 'raw'
                            ],
                            [
                                'attribute' => 'amount',
                                'format' => 'currency',
                                'contentOptions' => ['class' => 'h4 text-success fw-bold']
                            ],
                            [
                                'attribute' => 'category',
                                'value' => function($model) {
                                    return Expense::getCategories()[$model->category] ?? $model->category;
                                }
                            ],
                            [
                                'attribute' => 'status',
                                'value' => function($model) {
                                    $statuses = Expense::getStatuses();
                                    $class = 'status-' . $model->status;
                                    return Html::tag('span', $statuses[$model->status] ?? $model->status, [
                                        'class' => "badge badge-status $class fs-6"
                                    ]);
                                },
                                'format' => 'raw'
                            ],
                            'description:ntext',
                            [
                                'attribute' => 'submission_date',
                                'format' => 'datetime'
                            ],
                            [
                                'attribute' => 'review_date',
                                'format' => 'datetime',
                                'visible' => $model->status !== Expense::STATUS_PENDING
                            ],
                            [
                                'attribute' => 'reviewed_by',
                                'value' => function($model) {
                                    return $model->reviewedBy->username ?? 'N/A';
                                },
                                'visible' => $model->status !== Expense::STATUS_PENDING
                            ],
                        ],
                    ]) ?>
                </div>
            </div>

            <!-- Receipt Section -->
            <?php if ($model->receipt_file): ?>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Receipt Attachment</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $fileExt = pathinfo($model->receipt_file, PATHINFO_EXTENSION);
                    $isImage = in_array(strtolower($fileExt), ['jpg', 'jpeg', 'png', 'gif']);
                    ?>
                    
                    <?php if ($isImage): ?>
                        <img src="<?= $model->getReceiptUrl() ?>" 
                             class="img-fluid rounded shadow-sm mb-3" 
                             style="max-height: 300px" 
                             alt="Expense receipt">
                        <br>
                    <?php endif; ?>
                    
                    <?= Html::a(
                        '<i class="bi bi-download"></i> Download Receipt (' . strtoupper($fileExt) . ')', 
                        $model->getReceiptUrl(), 
                        [
                            'class' => 'btn btn-outline-primary',
                            'target' => '_blank',
                            'download' => 'receipt_' . $model->id . '.' . $fileExt
                        ]
                    ) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Review Actions -->
            <?php if ($model->status === Expense::STATUS_PENDING): ?>
            <div class="card mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Review Actions</h5>
                </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>
                    
                    <div class="d-grid gap-2 mb-3">
                        <?= Html::submitButton('<i class="bi bi-check-circle"></i> Approve Expense', [
                            'name' => 'action',
                            'value' => 'approve',
                            'class' => 'btn btn-success btn-lg',
                            'data' => [
                                'confirm' => 'Are you sure you want to approve this expense?',
                                'method' => 'post'
                            ]
                        ]) ?>
                        
                        <?= Html::submitButton('<i class="bi bi-x-circle"></i> Reject Expense', [
                            'name' => 'action',
                            'value' => 'reject',
                            'class' => 'btn btn-danger btn-lg',
                            'data' => [
                                'confirm' => 'Are you sure you want to reject this expense?',
                                'method' => 'post'
                            ]
                        ]) ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rejection Reason (Optional):</label>
                        <?= Html::textarea('comment', '', [
                            'class' => 'form-control',
                            'placeholder' => 'Please provide a reason for rejection...',
                            'rows' => 4,
                            'style' => 'resize: none;'
                        ]) ?>
                        <small class="form-text text-muted">This will be appended to the expense description.</small>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
            <?php else: ?>
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Review Status</h5>
                </div>
                <div class="card-body text-center">
                    <?php
                    $icon = $model->status === Expense::STATUS_APPROVED ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger';
                    $message = $model->status === Expense::STATUS_APPROVED ? 'Approved' : 'Rejected';
                    ?>
                    <i class="bi <?= $icon ?> fs-1 mb-3"></i>
                    <h4 class="text-<?= $model->status === Expense::STATUS_APPROVED ? 'success' : 'danger' ?>">
                        Expense <?= $message ?>
                    </h4>
                    <p class="text-muted mb-0">
                        by <?= $model->reviewedBy->username ?? 'Admin' ?><br>
                        on <?= Yii::$app->formatter->asDatetime($model->review_date) ?>
                    </p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::a('<i class="bi bi-arrow-left"></i> Back to List', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('<i class="bi bi-person"></i> View User Expenses', ['index', 'Expense[user_id]' => $model->user_id], ['class' => 'btn btn-outline-info']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>