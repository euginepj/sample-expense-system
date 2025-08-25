<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Expense;

$this->title = 'Submit New Expense';
$this->params['breadcrumbs'][] = ['label' => 'My Expenses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="expense-create">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="bi bi-plus-circle"></i> <?= Html::encode($this->title) ?></h4>
                </div>
                <div class="card-body">
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle"></i> <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>

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
                            ])->hint('Enter the total amount including taxes') ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'category')->dropDownList(
                                Expense::getCategories(),
                                [
                                    'prompt' => 'Select Category',
                                    'class' => 'form-select'
                                ]
                            )->hint('Choose the most appropriate category') ?>
                        </div>
                    </div>

                    <?= $form->field($model, 'description')->textarea([
                        'rows' => 4,
                        'placeholder' => 'Describe the purpose of this expense...'
                    ])->hint('Be specific about what this expense was for') ?>

                    <?= $form->field($model, 'receiptFile')->fileInput([
                        'class' => 'form-control'
                    ])->hint('Supported formats: PNG, JPG, PDF, DOC (Max 5MB). Receipts are required for amounts over $25.') ?>

                    <div class="form-group mt-4">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-outline-secondary me-md-2']) ?>
                            <?= Html::submitButton('<i class="bi bi-check-circle"></i> Submit Expense', ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>

            <!-- Submission Guidelines -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Submission Guidelines</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>✅ What's Acceptable:</h6>
                            <ul class="small">
                                <li>Business meals and entertainment</li>
                                <li>Travel expenses (flights, hotels, mileage)</li>
                                <li>Office supplies and equipment</li>
                                <li>Client-related expenses</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>❌ What's Not Allowed:</h6>
                            <ul class="small">
                                <li>Personal expenses</li>
                                <li>Alcohol (unless with clients)</li>
                                <li>Expenses without proper receipts</li>
                                <li>Expenses over 30 days old</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>