<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PayOS\PayOS;

class PayosHelper
{
    private $payos;

    public function __construct()
    {
        // Thay thế bằng thông tin từ tài khoản Payos của bạn
        $clientId = "355718b7-5f38-44ea-b31e-3e269705aa5b";
        $apiKey = "06eac658-bada-4cee-92a3-48626b520aab";
        $checksumKey = "91e022cb0777e5700cfe57d541e778bf9c744170c957423ac53cbf8e4d40448a";

        $this->payos = new PayOS($clientId, $apiKey, $checksumKey);
    }

    public function createPaymentLink($orderData)
    {
        $YOUR_DOMAIN = 'http://localhost:3000/index.php';
        echo "2";
        print_r($orderData["items"]);


        $data = [
            "orderCode" => (int) $orderData["id"],
            "amount" => (int) $orderData["total"] * 26000,
            "description" => "Thanh toán đơn hàng",
            "items" => $orderData["items"],
            "buyerName" => "tien chan",
            "buyerPhone" => "1111111111",
            "expiredAt" => time() + 30 * 60,
            "returnUrl" => "http://localhost:3000/?controller=checkout&action=success",
            "cancelUrl" => "http://localhost:3000/?controller=checkout&action=cancel",
        ];
        $response = $this->payos->createPaymentLink($data);

        return $response['checkoutUrl'];
    }

    public function cancelPayment($id)
    {
        $this->payos->cancelPaymentLink($id);
    }

    // public function getPaymentInfo($orderCode)
    // {
    //     try {
    //         return $this->payos->getPaymentLinkInformation($orderCode);
    //     } catch (\Exception $e) {
    //         error_log("PayOS Error: " . $e->getMessage());
    //         return null;
    //     }
    // }

    // public function verifyWebhookData($webhookData)
    // {
    //     try {
    //         return $this->payos->verifyPaymentWebhookData($webhookData);
    //     } catch (\Exception $e) {
    //         error_log("PayOS Webhook Error: " . $e->getMessage());
    //         return null;
    //     }
    // }
}
