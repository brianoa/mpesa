<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "mpesa_transactions".
 *
 * @property int $id
 * @property string $phone_number
 * @property float $amount
 * @property string|null $merchant_request_id
 * @property string|null $checkout_request_id
 * @property string|null $mpesa_receipt_number
 * @property string|null $result_code
 * @property string|null $result_description
 * @property string|null $status
 * @property string|null $created_at
 * @property string|null $updated_at
 */
class MpesaTransactions extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const STATUS_PENDING = 'PENDING';
    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'mpesa_transactions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_request_id', 'checkout_request_id', 'mpesa_receipt_number', 'result_code', 'result_description'], 'default', 'value' => null],
            [['status'], 'default', 'value' => 'PENDING'],
            [['phone_number', 'amount'], 'required'],
            [['amount'], 'number', 'min' => 1],
            [['phone_number'], 'match',
                'pattern' => '/^254[17][0-9]{8}$/',
                'message' => 'Enter a valid Kenyan phone number, e.g. 254712345678.'
            ],
            [['result_description', 'status'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['phone_number'], 'string', 'max' => 15],
            [['merchant_request_id', 'checkout_request_id'], 'string', 'max' => 100],
            [['mpesa_receipt_number'], 'string', 'max' => 50],
            [['result_code'], 'string', 'max' => 10],
            ['status', 'in', 'range' => array_keys(self::optsStatus())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone_number' => 'Phone Number',
            'amount' => 'Amount',
            'merchant_request_id' => 'Merchant Request ID',
            'checkout_request_id' => 'Checkout Request ID',
            'mpesa_receipt_number' => 'Mpesa Receipt Number',
            'result_code' => 'Result Code',
            'result_description' => 'Result Description',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }


    /**
     * column status ENUM value labels
     * @return string[]
     */
    public static function optsStatus()
    {
        return [
            self::STATUS_PENDING => 'PENDING',
            self::STATUS_SUCCESS => 'SUCCESS',
            self::STATUS_FAILED => 'FAILED',
        ];
    }

    /**
     * @return string
     */
   public function displayStatus()
    {
        return self::optsStatus()[$this->status] ?? 'Unknown';
    }

    /**
     * @return bool
     */
    public function isStatusPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function setStatusToPending()
    {
        $this->status = self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isStatusSuccess()
    {
        return $this->status === self::STATUS_SUCCESS;
    }

    public function setStatusToSuccess()
    {
        $this->status = self::STATUS_SUCCESS;
    }

    /**
     * @return bool
     */
    public function isStatusFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function setStatusToFailed()
    {
        $this->status = self::STATUS_FAILED;
    }
}
