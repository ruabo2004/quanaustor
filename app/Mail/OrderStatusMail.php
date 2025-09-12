<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $order;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, $oldStatus = null, $newStatus = null)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus ?? $order->status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->getSubjectByStatus($this->newStatus);
        
        return new Envelope(
            subject: $subject . ' - Đơn hàng #' . $this->order->id,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status',
            with: [
                'order' => $this->order,
                'oldStatus' => $this->oldStatus,
                'newStatus' => $this->newStatus,
                'statusMessage' => $this->getStatusMessage($this->newStatus)
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get email subject by status
     */
    private function getSubjectByStatus($status)
    {
        return match($status) {
            'pending' => 'Xác nhận đơn hàng',
            'processing' => 'Đơn hàng đang được xử lý',
            'shipped' => 'Đơn hàng đã được gửi',
            'delivered' => 'Đơn hàng đã được giao thành công',
            'cancelled' => 'Đơn hàng đã bị hủy',
            default => 'Cập nhật đơn hàng'
        };
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        return match($status) {
            'pending' => 'Cảm ơn bạn đã đặt hàng! Chúng tôi đã nhận được đơn hàng của bạn và đang xử lý.',
            'processing' => 'Đơn hàng của bạn đang được chuẩn bị. Chúng tôi sẽ thông báo khi hàng được gửi đi.',
            'shipped' => 'Đơn hàng của bạn đã được gửi đi và đang trên đường đến địa chỉ giao hàng.',
            'delivered' => 'Đơn hàng của bạn đã được giao thành công. Cảm ơn bạn đã mua sắm với chúng tôi!',
            'cancelled' => 'Đơn hàng của bạn đã bị hủy. Nếu có thắc mắc, vui lòng liên hệ với chúng tôi.',
            default => 'Đơn hàng của bạn đã được cập nhật.'
        };
    }
}
