<?php

namespace app\controllers;

use Yii;
use app\models\MpesaTransactions;
use app\models\MpesaTransactionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MpesaTransactionsController implements the CRUD actions for MpesaTransactions model.
 */
class MpesaTransactionsController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all MpesaTransactions models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MpesaTransactionsSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        $model = new MpesaTransactions();

        $successful = MpesaTransactions::find()
            ->where(['status' => MpesaTransactions::STATUS_SUCCESS])
            ->count();

        $pending = MpesaTransactions::find()
            ->where(['status' => MpesaTransactions::STATUS_PENDING])
            ->count();

        $failed = MpesaTransactions::find()
            ->where(['status' => MpesaTransactions::STATUS_FAILED])
            ->count();


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'successful' => $successful,
            'pending' => $pending,
            'failed' => $failed,
        ]);
    }
    /**
     * Record a payment request.
     * STK Push will be connected in the next stage.
     */
    public function actionPay()
    {
        $model = new MpesaTransactions();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $model->setStatusToPending();

            if ($model->save(false)) {
                Yii::$app->session->setFlash(
                    'success',
                    'Payment request recorded successfully. STK Push integration is our next step.'
                );
            } else {
                Yii::$app->session->setFlash(
                    'error',
                    'Unable to record the transaction.'
                );
            }

        } else {
            Yii::$app->session->setFlash(
                'error',
                'Please enter a valid phone number and payment amount.'
            );
        }

        return $this->redirect(['index']);
    }

    /**
     * Displays a single MpesaTransactions model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new MpesaTransactions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new MpesaTransactions();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MpesaTransactions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing MpesaTransactions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the MpesaTransactions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return MpesaTransactions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MpesaTransactions::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionTestConnection()
    {
        try {
            $mpesa = new \app\services\MpesaService();

            $token = $mpesa->getAccessToken();

            return $this->asJson([
                'success' => true,
                'message' => 'Successfully connected to Safaricom Daraja Sandbox.',
                'token_received' => !empty($token),
            ]);

        } catch (\Throwable $e) {

            Yii::error($e->getMessage(), __METHOD__);

            return $this->asJson([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function beforeAction($action)
    {
        if ($action->id === 'callback') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }


    public function actionCallback()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        try {
            $rawBody = Yii::$app->request->getRawBody();
            $data = json_decode($rawBody, true);

            $callback = $data['Body']['stkCallback'] ?? null;

            if (!$callback || !isset($callback['CheckoutRequestID'])) {
                Yii::error('Invalid M-Pesa callback: ' . $rawBody, __METHOD__);

                return [
                    'ResultCode' => 1,
                    'ResultDesc' => 'Invalid callback data',
                ];
            }

            $checkoutRequestId = $callback['CheckoutRequestID'];

            $transaction = MpesaTransactions::findOne([
                'checkout_request_id' => $checkoutRequestId,
            ]);

            if (!$transaction) {
                Yii::error(
                    'Transaction not found for CheckoutRequestID: ' . $checkoutRequestId,
                    __METHOD__
                );

                return [
                    'ResultCode' => 1,
                    'ResultDesc' => 'Transaction not found',
                ];
            }

            $resultCode = $callback['ResultCode'] ?? null;
            $resultDescription = $callback['ResultDesc'] ?? 'No description provided';

            $transaction->result_code = $resultCode;
            $transaction->result_description = $resultDescription;
            $transaction->merchant_request_id =
                $callback['MerchantRequestID'] ?? $transaction->merchant_request_id;

            if ((string)$resultCode === '0') {

                $transaction->setStatusToSuccess();

                $metadata = $callback['CallbackMetadata']['Item'] ?? [];

                foreach ($metadata as $item) {
                    if (!isset($item['Name'])) {
                        continue;
                    }

                    if ($item['Name'] === 'MpesaReceiptNumber') {
                        $transaction->mpesa_receipt_number = $item['Value'] ?? null;
                    }

                    if ($item['Name'] === 'Amount') {
                        $transaction->amount = $item['Value'] ?? $transaction->amount;
                    }
                }

            } else {
                $transaction->setStatusToFailed();
            }

            $transaction->updated_at = date('Y-m-d H:i:s');

            if (!$transaction->save(false)) {
                Yii::error(
                    'Unable to update transaction: ' . $checkoutRequestId,
                    __METHOD__
                );

                return [
                    'ResultCode' => 1,
                    'ResultDesc' => 'Unable to update transaction',
                ];
            }

            return [
                'ResultCode' => 0,
                'ResultDesc' => 'Callback processed successfully',
            ];

        } catch (\Throwable $e) {

            Yii::error($e->getMessage(), __METHOD__);

            return [
                'ResultCode' => 1,
                'ResultDesc' => 'Callback processing failed',
            ];
        }
    }
}
