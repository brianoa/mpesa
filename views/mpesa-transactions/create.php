<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactions $model */

$this->title = 'Create Mpesa Transactions';
$this->params['breadcrumbs'][] = ['label' => 'Mpesa Transactions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mpesa-transactions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
