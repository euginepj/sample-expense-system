<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Expense;

$this->title = 'Update Expense #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'My Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Expense #' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="expense-update">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil"></i> <?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> You can only edit expenses that are still pending review.
                    </div>

                    <?php if ($model->status !== Expense::STATUS_PENDING): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> This expense can no longer be edited because it has been <?= $model->status ?>.
                        </div>
                    <?php else: ?>
                        <?php $form = ActiveForm::begin([
                            'options' => ['enctype' => 'multipart/form-data'],
                            'fieldConfig' => [
                                'template' => "{label}\n{input}\n{error}\n{hint}",
                                'labelOptions' => ['class' => 'form-label fw-bold'],
                                'inputOptions' => ['class' => 'form-control'],
                                'errorOptions' => ['class' => 'invalid-feedback']
                            ]
                        ]); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?= $form->field($model, 'amount')->textInput([
                                    'type' => 'number', 
                                    'step' => '0.01',
                                    'placeholder' => '0.00',
                                    'min' => '0.01'
                                ]) ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($model, 'category')->dropDownList(
                                    Expense::getCategories(),
                                    ['class' => 'form-select']
                                ) ?>
                            </div>
                        </div>

                        <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

                        <?= $form->field($model, 'receiptFile')->fileInput()->hint('Current file: ' . ($model->receipt_file ?: 'None')) ?>

                        <div class="form-group mt-4">
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <?= Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-outline-secondary me-md-2']) ?>
                                <?= Html::submitButton('<i class="bi bi-check-circle"></i> Update Expense', ['class' => 'btn btn-warning']) ?>
                            </div>
                        </div>

                        <?php ActiveForm::end(); ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>