<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'guest_email',
        'guest_name',
        'subject',
        'description',
        'category',
        'priority',
        'status',
        'assigned_to',
        'department_id',
        'tags',
        'attachments',
        'order_id',
        'satisfaction_rating',
        'satisfaction_feedback',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'last_activity_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'attachments' => 'array',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(Str::random(8)) . '-' . date('Ymd');
            }
            $ticket->last_activity_at = now();
        });

        static::updating(function ($ticket) {
            $ticket->last_activity_at = now();
        });
    }

    /**
     * Get the user who created the ticket
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent assigned to the ticket
     */
    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the related order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get ticket replies
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    /**
     * Get public replies (visible to customer)
     */
    public function publicReplies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->where('is_internal', false);
    }

    /**
     * Get internal notes
     */
    public function internalNotes(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id')->where('is_internal', true);
    }

    /**
     * Scope for status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for priority
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for assigned agent
     */
    public function scopeAssignedTo($query, $agentId)
    {
        return $query->where('assigned_to', $agentId);
    }

    /**
     * Scope for open tickets
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['open', 'in_progress', 'waiting_customer']);
    }

    /**
     * Scope for closed tickets
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolved', 'closed']);
    }

    /**
     * Get customer name (user or guest)
     */
    public function getCustomerNameAttribute()
    {
        return $this->user ? $this->user->name : $this->guest_name;
    }

    /**
     * Get customer email (user or guest)
     */
    public function getCustomerEmailAttribute()
    {
        return $this->user ? $this->user->email : $this->guest_email;
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'open' => 'Mở',
            'in_progress' => 'Đang xử lý',
            'waiting_customer' => 'Chờ khách hàng',
            'resolved' => 'Đã giải quyết',
            'closed' => 'Đã đóng'
        ];

        return $statuses[$this->status] ?? 'Không xác định';
    }

    /**
     * Get priority display
     */
    public function getPriorityDisplayAttribute()
    {
        $priorities = [
            'low' => 'Thấp',
            'normal' => 'Bình thường',
            'high' => 'Cao',
            'urgent' => 'Khẩn cấp'
        ];

        return $priorities[$this->priority] ?? 'Bình thường';
    }

    /**
     * Get category display
     */
    public function getCategoryDisplayAttribute()
    {
        $categories = [
            'general' => 'Tổng quan',
            'orders' => 'Đặt hàng',
            'shipping' => 'Vận chuyển',
            'returns' => 'Đổi trả',
            'payments' => 'Thanh toán',
            'products' => 'Sản phẩm',
            'technical' => 'Kỹ thuật',
            'account' => 'Tài khoản'
        ];

        return $categories[$this->category] ?? 'Khác';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'open' => 'warning',
            'in_progress' => 'info',
            'waiting_customer' => 'secondary',
            'resolved' => 'success',
            'closed' => 'dark'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Get priority color
     */
    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'success',
            'normal' => 'primary',
            'high' => 'warning',
            'urgent' => 'danger'
        ];

        return $colors[$this->priority] ?? 'primary';
    }

    /**
     * Get age in hours
     */
    public function getAgeInHoursAttribute()
    {
        return $this->created_at->diffInHours(now());
    }

    /**
     * Get time since last activity
     */
    public function getTimeSinceLastActivityAttribute()
    {
        return $this->last_activity_at->diffForHumans();
    }

    /**
     * Check if ticket is overdue
     */
    public function getIsOverdueAttribute()
    {
        $slaHours = [
            'urgent' => 1,
            'high' => 4,
            'normal' => 24,
            'low' => 48
        ];

        $maxHours = $slaHours[$this->priority] ?? 24;
        return $this->age_in_hours > $maxHours && in_array($this->status, ['open', 'in_progress']);
    }

    /**
     * Assign to agent
     */
    public function assignTo($agentId)
    {
        $this->update([
            'assigned_to' => $agentId,
            'status' => 'in_progress'
        ]);

        // Add system note
        $this->replies()->create([
            'user_id' => $agentId,
            'message' => 'Ticket được gán cho ' . User::find($agentId)->name,
            'type' => 'status_change',
            'is_internal' => true
        ]);
    }

    /**
     * Update status
     */
    public function updateStatus($newStatus, $userId = null, $message = null)
    {
        $oldStatus = $this->status;
        
        $this->update(['status' => $newStatus]);

        if ($newStatus === 'resolved') {
            $this->update(['resolved_at' => now()]);
        } elseif ($newStatus === 'closed') {
            $this->update(['closed_at' => now()]);
        }

        // Add system note
        if ($message) {
            $this->replies()->create([
                'user_id' => $userId,
                'message' => $message,
                'type' => 'status_change',
                'is_internal' => false,
                'metadata' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]
            ]);
        }
    }

    /**
     * Add reply
     */
    public function addReply($message, $userId, $isInternal = false, $attachments = null)
    {
        $reply = $this->replies()->create([
            'user_id' => $userId,
            'message' => $message,
            'is_internal' => $isInternal,
            'attachments' => $attachments
        ]);

        // Update first response time if this is the first agent reply
        if (!$this->first_response_at && !$isInternal) {
            $user = User::find($userId);
            if ($user && $user->isAdmin()) {
                $this->update(['first_response_at' => now()]);
            }
        }

        return $reply;
    }

    /**
     * Get response time metrics
     */
    public function getResponseTimeMetrics()
    {
        return [
            'first_response_time' => $this->first_response_at ? 
                $this->created_at->diffInMinutes($this->first_response_at) : null,
            'resolution_time' => $this->resolved_at ? 
                $this->created_at->diffInMinutes($this->resolved_at) : null,
            'is_within_sla' => !$this->is_overdue
        ];
    }
}
