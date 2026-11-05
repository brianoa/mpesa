<?php

namespace app\services;

use Yii;
use yii\base\Component;
use yii\base\Exception;

class MpesaService extends Component
{
    /**
     * Load M-Pesa configuration.
     */
    private function getMpesaConfig()
    {
        $configFile = Yii::getAlias('@app/config/params-local.php');

        if (!file_exists($configFile)) {
            throw new Exception('M-Pesa configuration file was not found.');
        }

        $config = require $configFile;
        $mpesa = $config['mpesa'] ?? [];

        $required = [
            'consumerKey',
            'consumerSecret',
            'shortcode',
            'passkey',
        ];

        foreach ($required as $key) {
            if (empty($mpesa[$key])) {
                throw new Exception("Missing M-Pesa configuration: $key");
            }
        }

        return $mpesa;
    }

    /**
     * Generate Safaricom OAuth access token.
     */
    public function getAccessToken()
    {
        $mpesa = $this->getMpesaConfig();

        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $mpesa['consumerKey'] . ':' . $mpesa['consumerSecret'],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($response === false) {
            throw new Exception('Safaricom connection failed: ' . $curlError);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200 || empty($result['access_token'])) {
            throw new Exception('Safaricom authentication failed. HTTP: ' . $httpCode);
        }

        return $result['access_token'];
    }

    /**
     * Initiate an M-Pesa STK Push request.
     */
    public function initiateStkPush(
        $phoneNumber,
        $amount,
        $accountReference,
        $transactionDesc,
        $callbackUrl
    ) {
        $mpesa = $this->getMpesaConfig();

        // Validate phone number.
        if (!preg_match('/^254[17][0-9]{8}$/', $phoneNumber)) {
            throw new Exception('Invalid phone number. Use format 2547XXXXXXXX.');
        }

        // Validate amount.
        if (!is_numeric($amount) || (int)$amount < 1) {
            throw new Exception('Amount must be at least KSh 1.');
        }

        // Callback must be a publicly accessible HTTPS URL.
        if (
            !filter_var($callbackUrl, FILTER_VALIDATE_URL) ||
            parse_url($callbackUrl, PHP_URL_SCHEME) !== 'https'
        ) {
            throw new Exception('A valid public HTTPS callback URL is required.');
        }

        // Generate timestamp using Kenya time.
        $timestamp = (new \DateTime(
            'now',
            new \DateTimeZone('Africa/Nairobi')
        ))->format('YmdHis');

        // Generate STK Push password.
        $password = base64_encode(
            $mpesa['shortcode'] .
            $mpesa['passkey'] .
            $timestamp
        );

        // Get access token.
        $accessToken = $this->getAccessToken();

        // Safaricom STK Push endpoint.
        $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

        // Prepare request data.
        $payload = [
            'BusinessShortCode' => $mpesa['shortcode'],
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int)$amount,
            'PartyA' => $phoneNumber,
            'PartyB' => $mpesa['shortcode'],
            'PhoneNumber' => $phoneNumber,
            'CallBackURL' => $callbackUrl,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($response === false) {
            throw new Exception('STK Push connection failed: ' . $curlError);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200 || !is_array($result)) {
            throw new Exception(
                'STK Push request failed. HTTP status: ' . $httpCode
            );
        }

        return $result;
    }
}
