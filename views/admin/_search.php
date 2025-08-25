<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Expense;
use app\models\User;

$form = ActiveForm::begin([
    'action' => ['index'],
    'method' => 'get',
    'options' => ['class' => 'row g-3']
]);
?>

<div class="col-md-3">
    <?= $form->field($model, 'user_id')->dropDownList(
        \yii\helpers\ArrayHelper::map(User::find()->all(), 'id', 'username'),
        ['prompt' => 'All Users', 'class' => 'form-select']
    ) ?>
</div>

<div class="col-md-3">
    <?= $form->field($model, 'status')->dropDownList(
        Expense::getStatuses(),
        ['prompt' => 'All Statuses', 'class' => 'form-select']
    ) ?>
</div>

<div class="col-md-3">
    <?= $form->field($model, 'category')->dropDownList(
        Expense::getCategories(),
        ['prompt' => 'All Categories', 'class' => 'form-select']
    ) ?>
</div>

<div class="col-md-3">
    <?= $form->field($model, 'description')->textInput([
        'placeholder' => 'Search description...'
    ]) ?>
</div>

<div class="col-12">
    <div class="btn-group">
        <?= Html::submitButton('<i class="bi bi-search"></i> Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="bi bi-x-circle"></i> Clear', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>