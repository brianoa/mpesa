<?php

namespace app\services;

use Yii;
use yii\base\Component;
use yii\base\Exception;

class MpesaService extends Component
{
    /**
     * Generate an OAuth access token from Safaricom.
     */
    public function getAccessToken()
    {
        $configFile = Yii::getAlias('@app/config/params.php');

        if (!file_exists($configFile)) {
            throw new Exception('M-Pesa configuration file was not found.');
        }

        $config = require $configFile;
        $mpesa = $config['mpesa'] ?? [];

        $consumerKey = $mpesa['consumerKey'] ?? '';
        $consumerSecret = $mpesa['consumerSecret'] ?? '';

        if (empty($consumerKey) || empty($consumerSecret)) {
            throw new Exception('M-Pesa credentials are missing.');
        }

        $url = 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_USERPWD => $consumerKey . ':' . $consumerSecret,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($response === false) {
            throw new Exception('Connection to Safaricom failed: ' . $curlError);
        }

        $result = json_decode($response, true);

        if ($httpCode !== 200 || empty($result['access_token'])) {
            throw new Exception(
                'Safaricom authentication failed. HTTP status: ' . $httpCode
            );
        }

        return $result['access_token'];
    }
}
