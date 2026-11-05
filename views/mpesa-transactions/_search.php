<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactionsSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mpesa-transactions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'phone_number') ?>

    <?= $form->field($model, 'amount') ?>

    <?= $form->field($model, 'merchant_request_id') ?>

    <?= $form->field($model, 'checkout_request_id') ?>

    <?php // echo $form->field($model, 'mpesa_receipt_number') ?>

    <?php // echo $form->field($model, 'result_code') ?>

    <?php // echo $form->field($model, 'result_description') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
