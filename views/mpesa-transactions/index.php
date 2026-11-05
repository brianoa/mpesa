
<?php

use app\models\MpesaTransactions;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MpesaTransactionsSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var app\models\MpesaTransactions $model */

$this->title = 'M-Pesa Payment System';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="mpesa-transactions-index">

    <!-- Page Header -->
    <div class="mb-4">
        <h2><?= Html::encode($this->title) ?></h2>
        <p class="text-muted">
            Initiate payments and monitor M-Pesa transactions.
        </p>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Successful Payments</h6>
                    <h2 class="text-success">
                        <?= number_format($successful) ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Pending Payments</h6>
                    <h2 class="text-warning">
                        <?= number_format($pending) ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted">Failed Payments</h6>
                    <h2 class="text-danger">
                        <?= number_format($failed) ?>
                    </h2>
                </div>
            </div>
        </div>

    </div>

    <!-- Payment Form -->
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Make an M-Pesa Payment</h5>
        </div>

        <div class="card-body">

            <?php $form = ActiveForm::begin([
                'action' => ['pay'],
                'method' => 'post',
            ]); ?>

            <div class="row">

                <div class="col-md-5">
                    <?= $form->field($model, 'phone_number')
                        ->textInput([
                            'maxlength' => 15,
                            'placeholder' => '254712345678',
                        ]) ?>
                </div>

                <div class="col-md-4">
                    <?= $form->field($model, 'amount')
                        ->input('number', [
                            'min' => 1,
                            'step' => 1,
                            'placeholder' => 'Enter amount',
                        ]) ?>
                </div>

                <div class="col-md-3 d-flex align-items-end mb-3">

                    <?= Html::submitButton(
                        '<i class="fas fa-mobile-alt"></i> Pay with M-Pesa',
                        [
                            'class' => 'btn btn-success w-100',
                        ]
                    ) ?>

                </div>

            </div>

            <?php ActiveForm::end(); ?>

            <small class="text-muted">
                Enter the customer's Safaricom phone number and payment amount.
            </small>

        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <!-- Transactions Table -->
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Transaction History</h5>
        </div>

        <div class="card-body">

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,

                'tableOptions' => [
                    'class' => 'table table-hover table-striped align-middle',
                ],

                'columns' => [

                    ['class' => 'yii\grid\SerialColumn'],

                    'phone_number',

                    [
                        'attribute' => 'amount',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return 'KSh ' . number_format(
                                (float) $model->amount, 2
                            );
                        },
                    ],

                    'mpesa_receipt_number',

                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'filter' => MpesaTransactions::optsStatus(),
                        'value' => function ($model) {

                            $color = 'secondary';

                            if ($model->status === MpesaTransactions::STATUS_SUCCESS) {
                                $color = 'success';
                            } elseif ($model->status === MpesaTransactions::STATUS_PENDING) {
                                $color = 'warning';
                            } elseif ($model->status === MpesaTransactions::STATUS_FAILED) {
                                $color = 'danger';
                            }

                            return Html::tag(
                                'span',
                                Html::encode($model->displayStatus()),
                                ['class' => 'badge bg-' . $color]
                            );
                        },
                    ],

                    [
                        'attribute' => 'created_at',
                        'format' => ['datetime', 'php:d M Y, H:i'],
                    ],

                    [
                        'class' => ActionColumn::class,
                        'template' => '{view}',
                        'urlCreator' => function ($action, MpesaTransactions $model) {
                            return Url::toRoute([
                                $action,
                                'id' => $model->id
                            ]);
                        },
                    ],

                ],
            ]); ?>

        </div>
    </div>

</div>
