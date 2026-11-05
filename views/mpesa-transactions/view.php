
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\YiiAsset;

$this->title = 'Transaction #' . $model->id;

$this->params['breadcrumbs'][] = [
    'label' => 'M-Pesa Transactions',
    'url' => ['index']
];

$this->params['breadcrumbs'][] = $this->title;

YiiAsset::register($this);

?>

<div class="mpesa-transactions-view">

    <div class="mb-4">
        <h2><?= Html::encode($this->title) ?></h2>

        <p class="text-muted">
            View M-Pesa transaction details.
        </p>
    </div>

    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">Payment Information</h5>
        </div>

        <div class="card-body">

            <?= DetailView::widget([
                'model' => $model,

                'attributes' => [

                    'id',

                    'phone_number',

                    [
                        'attribute' => 'amount',
                        'value' => 'KSh ' . number_format(
                            (float) $model->amount, 2
                        ),
                    ],

                    'merchant_request_id',

                    'checkout_request_id',

                    'mpesa_receipt_number',

                    'result_code',

                    'result_description:ntext',

                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {

                            $color = 'secondary';

                            if ($model->status === 'SUCCESS') {
                                $color = 'success';
                            } elseif ($model->status === 'PENDING') {
                                $color = 'warning';
                            } elseif ($model->status === 'FAILED') {
                                $color = 'danger';
                            }

                            return Html::tag(
                                'span',
                                Html::encode($model->displayStatus()),
                                ['class' => 'badge bg-' . $color]
                            );
                        },
                    ],

                    'created_at:datetime',

                    'updated_at:datetime',

                ],
            ]) ?>

            <div class="mt-3">

                <?= Html::a(
                    'Back to Transactions',
                    ['index'],
                    ['class' => 'btn btn-secondary']
                ) ?>

            </div>

        </div>
    </div>

</div>
