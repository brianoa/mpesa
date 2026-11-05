<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactions $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mpesa-transactions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'merchant_request_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'checkout_request_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'mpesa_receipt_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'result_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'result_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->dropDownList([ 'PENDING' => 'PENDING', 'SUCCESS' => 'SUCCESS', 'FAILED' => 'FAILED', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
