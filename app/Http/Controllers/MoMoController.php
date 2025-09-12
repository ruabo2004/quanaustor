<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MoMoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MoMoController extends Controller
{
    private $momoService;

    public function __construct()
    {
        $this->momoService = new MoMoService();
    }

    /**
     * Handle MoMo return URL
     */
    public function return(Request $request)
    {
        $resultCode = $request->get('resultCode');
        $orderId = $request->get('orderId');
        $message = $request->get('message');

        Log::info('MoMo Return', $request->all());

        if ($this->momoService->isPaymentSuccessful($resultCode)) {
            // Payment successful
            $this->updateOrderStatus($orderId, 'paid');
            
            return redirect()->route('orders.index')
                ->with('success', 'Thanh toán MoMo thành công! Đơn hàng của bạn đã được xác nhận.');
        } else {
            // Payment failed
            $this->updateOrderStatus($orderId, 'failed');
            $statusText = $this->momoService->getPaymentStatusText($resultCode);
            
            return redirect()->route('cart.index')
                ->with('error', 'Thanh toán MoMo thất bại: ' . $statusText);
        }
    }

    /**
     * Handle MoMo IPN (Instant Payment Notification)
     */
    public function ipn(Request $request)
    {
        Log::info('MoMo IPN Received', $request->all());

        try {
            // Verify signature
            if (!$this->momoService->verifyIpnSignature($request->all())) {
                Log::error('MoMo IPN: Invalid signature', $request->all());
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 400);
            }

            $resultCode = $request->get('resultCode');
            $orderId = $request->get('orderId');

            if ($this->momoService->isPaymentSuccessful($resultCode)) {
                $this->updateOrderStatus($orderId, 'paid');
                Log::info('MoMo IPN: Payment confirmed for order ' . $orderId);
            } else {
                $this->updateOrderStatus($orderId, 'failed');
                Log::info('MoMo IPN: Payment failed for order ' . $orderId . ' with code ' . $resultCode);
            }

            // Return success response to MoMo
            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('MoMo IPN Error: ' . $e->getMessage(), $request->all());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update order payment status
     */
    private function updateOrderStatus($momoOrderId, $status)
    {
        try {
            $order = Order::where('momo_order_id', $momoOrderId)->first();
            
            if ($order) {
                $order->update(['payment_status' => $status]);
                Log::info("Order #{$order->id} payment status updated to: {$status}");
            } else {
                Log::warning("Order not found for MoMo Order ID: {$momoOrderId}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to update order status: " . $e->getMessage());
        }
    }
}
