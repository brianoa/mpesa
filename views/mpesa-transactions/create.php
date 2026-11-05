
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactions $model */

$this->title = 'New M-Pesa Transaction';
$this->params['breadcrumbs'][] = [
    'label' => 'M-Pesa Transactions',
    'url' => ['index']
];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="mpesa-transactions-create">

    <div class="mb-4">
        <h3><?= Html::encode($this->title) ?></h3>
        <p class="text-muted">
            Enter the customer's phone number and payment amount.
        </p>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>

        </div>
    </div>

</div>
