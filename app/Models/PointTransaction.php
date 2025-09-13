<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'transaction_id',
        'type',
        'points',
        'balance_before',
        'balance_after',
        'source_type',
        'source_id',
        'description',
        'metadata',
        'order_id',
        'order_total',
        'order_status',
        'expires_at',
        'is_expired',
        'expired_by_transaction_id',
        'admin_user_id',
        'admin_notes',
        'status'
    ];

    protected $casts = [
        'metadata' => 'array',
        'order_total' => 'decimal:2',
        'expires_at' => 'date',
        'is_expired' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = 'TXN_' . strtoupper(Str::random(10)) . '_' . time();
            }
        });
    }

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the admin user who made the transaction
     */
    public function adminUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    /**
     * Scope for earned points
     */
    public function scopeEarned($query)
    {
        return $query->where('type', 'earned');
    }

    /**
     * Scope for spent points
     */
    public function scopeSpent($query)
    {
        return $query->where('type', 'spent');
    }

    /**
     * Scope for non-expired points
     */
    public function scopeNotExpired($query)
    {
        return $query->where('is_expired', false);
    }

    /**
     * Scope for expiring soon
     */
    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('type', 'earned')
            ->where('is_expired', false)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now()->addDays($days));
    }

    /**
     * Get transaction type icon
     */
    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'earned' => 'fas fa-plus-circle text-success',
            'spent' => 'fas fa-minus-circle text-danger',
            'expired' => 'fas fa-clock text-warning',
            'adjusted' => 'fas fa-edit text-info',
            'bonus' => 'fas fa-gift text-primary',
            'refund' => 'fas fa-undo text-info',
            default => 'fas fa-circle text-muted'
        };
    }

    /**
     * Get transaction type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'earned' => 'Tích điểm',
            'spent' => 'Tiêu điểm',
            'expired' => 'Hết hạn',
            'adjusted' => 'Điều chỉnh',
            'bonus' => 'Thưởng',
            'refund' => 'Hoàn điểm',
            default => 'Khác'
        };
    }

    /**
     * Get formatted points with sign
     */
    public function getFormattedPointsAttribute()
    {
        $sign = in_array($this->type, ['earned', 'bonus', 'refund']) ? '+' : '-';
        return $sign . number_format(abs($this->points));
    }

    /**
     * Check if transaction is expiring soon
     */
    public function isExpiringSoon($days = 30)
    {
        return $this->type === 'earned' 
            && !$this->is_expired 
            && $this->expires_at 
            && $this->expires_at->lte(now()->addDays($days));
    }

    /**
     * Mark points as expired
     */
    public function markAsExpired($expiredByTransactionId = null)
    {
        $this->update([
            'is_expired' => true,
            'expired_by_transaction_id' => $expiredByTransactionId
        ]);
    }

    /**
     * Get source display name
     */
    public function getSourceDisplayAttribute()
    {
        return match($this->source_type) {
            'Order' => 'Đơn hàng #' . $this->source_id,
            'Referral' => 'Giới thiệu bạn bè',
            'Birthday' => 'Thưởng sinh nhật',
            'Admin' => 'Điều chỉnh từ quản trị',
            'Registration' => 'Thưởng đăng ký',
            'Review' => 'Thưởng đánh giá',
            'Social' => 'Thưởng chia sẻ mạng xã hội',
            'Event' => 'Sự kiện đặc biệt',
            'Reward' => 'Đổi phần thưởng',
            default => $this->source_type
        };
    }
}
