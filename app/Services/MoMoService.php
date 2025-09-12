<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoMoService
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endpoint;

    public function __construct()
    {
        $this->partnerCode = config('services.momo.partner_code');
        $this->accessKey = config('services.momo.access_key');
        $this->secretKey = config('services.momo.secret_key');
        $this->endpoint = config('services.momo.endpoint');
    }

    /**
     * Create MoMo payment request
     * Based on: https://github.com/momo-wallet/payment/tree/d4746c5d1cc21d4efc96b3ad24cd0e19244c1dbd/php
     */
    public function createPayment($orderInfo, $amount, $orderId, $redirectUrl = null, $ipnUrl = null, $requestType = 'captureWallet')
    {
        $requestId = time() . "";
        // Use parameter or default to captureWallet for payment options
        // $requestType is now passed as parameter
        $extraData = "";
        
        // Use config URLs if not provided
        $redirectUrl = $redirectUrl ?? config('services.momo.redirect_url');
        $ipnUrl = $ipnUrl ?? config('services.momo.ipn_url');

        // Create raw signature string
        $rawHash = "accessKey=" . $this->accessKey . 
                  "&amount=" . $amount . 
                  "&extraData=" . $extraData . 
                  "&ipnUrl=" . $ipnUrl . 
                  "&orderId=" . $orderId . 
                  "&orderInfo=" . $orderInfo . 
                  "&partnerCode=" . $this->partnerCode . 
                  "&redirectUrl=" . $redirectUrl . 
                  "&requestId=" . $requestId . 
                  "&requestType=" . $requestType;

        // Generate signature
        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'partnerName' => "Quan Au Store",
            'storeId' => "QuanAuStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->endpoint, $data);

            $result = $response->json();

            Log::info('MoMo Payment Request', [
                'data' => $data,
                'response' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('MoMo Payment Error', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);

            return [
                'resultCode' => 99,
                'message' => 'System error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Verify MoMo IPN signature
     */
    public function verifyIpnSignature($data)
    {
        $rawHash = "accessKey=" . $this->accessKey . 
                  "&amount=" . $data['amount'] . 
                  "&extraData=" . $data['extraData'] . 
                  "&message=" . $data['message'] . 
                  "&orderId=" . $data['orderId'] . 
                  "&orderInfo=" . $data['orderInfo'] . 
                  "&orderType=" . $data['orderType'] . 
                  "&partnerCode=" . $data['partnerCode'] . 
                  "&payType=" . $data['payType'] . 
                  "&requestId=" . $data['requestId'] . 
                  "&responseTime=" . $data['responseTime'] . 
                  "&resultCode=" . $data['resultCode'] . 
                  "&transId=" . $data['transId'];

        $expectedSignature = hash_hmac("sha256", $rawHash, $this->secretKey);

        return hash_equals($expectedSignature, $data['signature']);
    }

    /**
     * Check if payment was successful
     */
    public function isPaymentSuccessful($resultCode)
    {
        return $resultCode == 0;
    }

    /**
     * Get payment status text
     */
    public function getPaymentStatusText($resultCode)
    {
        $statusMap = [
            0 => 'Giao dịch thành công',
            9000 => 'Giao dịch được ủy quyền (authorization) thành công',
            8000 => 'Giao dịch đang được xử lý',
            7000 => 'Trừ tiền thành công. Giao dịch bị nghi ngờ (fraud). Giao dịch sẽ được hoàn tiền sau',
            1000 => 'Giao dịch được khởi tạo, chờ người dùng xác nhận thanh toán',
            4001 => 'Số tiền giao dịch vượt quá hạn mức thanh toán của người dùng',
            4100 => 'Giao dịch bị từ chối do tài khoản người dùng bị khóa',
            2001 => 'Giao dịch thất bại do sai thông tin liên kết',
            2007 => 'Giao dịch thất bại do thông tin giao dịch không hợp lệ',
            49 => 'Lỗi không xác định',
            10 => 'Lỗi không xác định',
            11 => 'Giao dịch không thành công do hết hạn thanh toán',
            12 => 'Giao dịch không thành công do tài khoản người dùng bị khóa',
            13 => 'Giao dịch không thành công do sai mật khẩu giao dịch',
            4006 => 'Giao dịch không thành công do vượt quá số lần nhập sai mật khẩu',
            9999 => 'Giao dịch thất bại'
        ];

        return $statusMap[$resultCode] ?? 'Trạng thái không xác định';
    }
}
