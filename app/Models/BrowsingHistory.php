<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrowsingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'product_id',
        'ip_address',
        'user_agent',
        'view_duration',
        'interaction_data',
        'viewed_at'
    ];

    protected $casts = [
        'interaction_data' => 'array',
        'viewed_at' => 'datetime',
        'view_duration' => 'integer'
    ];

    /**
     * Get the user that viewed the product
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the product that was viewed
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for recent views
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('viewed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for user views
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for session views (guest users)
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Get views for user or session
     */
    public function scopeForUserOrSession($query, $userId = null, $sessionId = null)
    {
        return $query->where(function($q) use ($userId, $sessionId) {
            if ($userId) {
                $q->where('user_id', $userId);
            }
            if ($sessionId) {
                $q->orWhere('session_id', $sessionId);
            }
        });
    }
}
