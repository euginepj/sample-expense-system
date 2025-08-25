<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Expense;

$this->title = 'Expense Details #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'My Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-view">
    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h4 class="mb-0">Expense Information</h4>
                </div>
                <div class="card-body">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            [
                                'attribute' => 'amount',
                                'format' => 'currency',
                                'contentOptions' => ['class' => 'h3 text-success fw-bold']
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
                            [
                                'attribute' => 'category',
                                'value' => function($model) {
                                    return Expense::getCategories()[$model->category] ?? $model->category;
                                }
                            ],
                            'description:ntext',
                            [
                                'attribute' => 'submission_date',
                                'format' => 'datetime',
                                'label' => 'Submitted On'
                            ],
                            [
                                'attribute' => 'review_date',
                                'format' => 'datetime',
                                'visible' => $model->status !== Expense::STATUS_PENDING,
                                'label' => 'Reviewed On'
                            ],
                            [
                                'attribute' => 'reviewed_by',
                                'value' => function($model) {
                                    return $model->reviewedBy->username ?? 'N/A';
                                },
                                'visible' => $model->status !== Expense::STATUS_PENDING,
                                'label' => 'Reviewed By'
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
                    
                    <div class="mt-2">
                        <small class="text-muted">File: <?= $model->receipt_file ?></small>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <!-- Status Information -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Status Information</h5>
                </div>
                <div class="card-body">
                    <?php if ($model->status === Expense::STATUS_PENDING): ?>
                        <div class="text-center">
                            <i class="bi bi-clock-history text-warning fs-1"></i>
                            <h4 class="text-warning">Pending Review</h4>
                            <p class="text-muted">Your expense is waiting for admin approval. This usually takes 1-2 business days.</p>
                        </div>
                    <?php elseif ($model->status === Expense::STATUS_APPROVED): ?>
                        <div class="text-center">
                            <i class="bi bi-check-circle text-success fs-1"></i>
                            <h4 class="text-success">Approved</h4>
                            <p class="text-muted">Your expense has been approved and will be processed for reimbursement.</p>
                            <?php if ($model->review_date): ?>
                                <small class="text-muted">Approved on: <?= Yii::$app->formatter->asDatetime($model->review_date) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <i class="bi bi-x-circle text-danger fs-1"></i>
                            <h4 class="text-danger">Rejected</h4>
                            <p class="text-muted">Your expense was not approved. Please check the description for details.</p>
                            <?php if ($model->review_date): ?>
                                <small class="text-muted">Rejected on: <?= Yii::$app->formatter->asDatetime($model->review_date) ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <?= Html::a('<i class="bi bi-arrow-left"></i> Back to List', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
                        <?= Html::a('<i class="bi bi-plus-circle"></i> Submit Another', ['create'], ['class' => 'btn btn-outline-success']) ?>
                        
                        <?php if ($model->status === Expense::STATUS_PENDING): ?>
                            <?= Html::a('<i class="bi bi-pencil"></i> Edit Expense', ['update', 'id' => $model->id], [
                                'class' => 'btn btn-outline-warning',
                                'data' => ['confirm' => 'Are you sure you want to edit this expense?']
                            ]) ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Need Help? -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-question-circle"></i> Need Help?</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">If you have questions about this expense:</p>
                    <ul class="small">
                        <li>Email: expenses@company.com</li>
                        <li>Phone: x1234 (HR Department)</li>
                        <li>Office: Building A, Floor 3</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>