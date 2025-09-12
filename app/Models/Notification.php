<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'title',
        'message',
        'data',
        'read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime'
    ];

    // Notification types
    const TYPE_ORDER_STATUS = 'order_status';
    const TYPE_EMAIL = 'email';
    const TYPE_SYSTEM = 'system';
    const TYPE_PROMOTION = 'promotion';

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    /**
     * Scope for user notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for system notifications (for all users)
     */
    public function scopeSystem($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => Carbon::now()
        ]);
    }

    /**
     * Get time ago format
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get notification icon based on type
     */
    public function getIconAttribute()
    {
        return match($this->type) {
            self::TYPE_ORDER_STATUS => 'fas fa-shopping-bag',
            self::TYPE_EMAIL => 'fas fa-envelope',
            self::TYPE_SYSTEM => 'fas fa-cog',
            self::TYPE_PROMOTION => 'fas fa-tags',
            default => 'fas fa-bell'
        };
    }

    /**
     * Get notification color based on type
     */
    public function getColorAttribute()
    {
        return match($this->type) {
            self::TYPE_ORDER_STATUS => 'primary',
            self::TYPE_EMAIL => 'info',
            self::TYPE_SYSTEM => 'warning',
            self::TYPE_PROMOTION => 'success',
            default => 'secondary'
        };
    }

    /**
     * Create notification for user
     */
    public static function createForUser($userId, $type, $title, $message, $data = null)
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Create system notification (for all users)
     */
    public static function createSystem($type, $title, $message, $data = null)
    {
        return self::create([
            'user_id' => null,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Create order status notification
     */
    public static function createOrderStatus($userId, $orderId, $status, $orderTotal = null)
    {
        $statusMessages = [
            'pending' => 'Đơn hàng đang chờ xử lý',
            'processing' => 'Đơn hàng đang được xử lý',
            'shipped' => 'Đơn hàng đã được gửi',
            'delivered' => 'Đơn hàng đã được giao thành công',
            'cancelled' => 'Đơn hàng đã bị hủy'
        ];

        $title = $statusMessages[$status] ?? 'Cập nhật đơn hàng';
        $message = "Đơn hàng #{$orderId} " . strtolower($title);
        
        if ($orderTotal) {
            $message .= " với tổng giá trị " . number_format($orderTotal) . " VNĐ";
        }

        return self::createForUser($userId, self::TYPE_ORDER_STATUS, $title, $message, [
            'order_id' => $orderId,
            'status' => $status,
            'total' => $orderTotal
        ]);
    }
}
