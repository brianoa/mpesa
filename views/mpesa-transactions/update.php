
<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactions $model */

$this->title = 'Update Transaction #' . $model->id;

$this->params['breadcrumbs'][] = [
    'label' => 'M-Pesa Transactions',
    'url' => ['index']
];

$this->params['breadcrumbs'][] = [
    'label' => 'Transaction #' . $model->id,
    'url' => ['view', 'id' => $model->id]
];

$this->params['breadcrumbs'][] = 'Update';
?>

<div class="mpesa-transactions-update">

    <div class="mb-4">
        <h3><?= Html::encode($this->title) ?></h3>
        <p class="text-muted">
            Update transaction information.
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
