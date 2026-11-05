
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactions $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="mpesa-transactions-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">

        <div class="col-md-6">
            <?= $form->field($model, 'phone_number')->textInput([
                'maxlength' => true,
                'placeholder' => '254712345678',
            ]) ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'amount')->input('number', [
                'min' => 1,
                'step' => 1,
                'placeholder' => 'Enter amount in KSh',
            ]) ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton('Save Transaction', [
            'class' => 'btn btn-success'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
