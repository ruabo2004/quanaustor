<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FAQ extends Model
{
    use HasFactory;

    protected $table = 'f_a_q_s';

    protected $fillable = [
        'question',
        'answer',
        'category',
        'tags',
        'view_count',
        'helpful_count',
        'not_helpful_count',
        'sort_order',
        'is_featured',
        'is_published',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_published' => 'boolean'
    ];

    /**
     * Get the creator of the FAQ
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater of the FAQ
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for published FAQs
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for featured FAQs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for category
     */
    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('question', 'LIKE', "%{$searchTerm}%")
              ->orWhere('answer', 'LIKE', "%{$searchTerm}%")
              ->orWhereJsonContains('tags', $searchTerm);
        });
    }

    /**
     * Get category display name
     */
    public function getCategoryDisplayAttribute()
    {
        $categories = [
            'general' => 'Tổng quan',
            'orders' => 'Đặt hàng',
            'shipping' => 'Vận chuyển',
            'returns' => 'Đổi trả',
            'payments' => 'Thanh toán',
            'products' => 'Sản phẩm'
        ];

        return $categories[$this->category] ?? 'Khác';
    }

    /**
     * Get category icon
     */
    public function getCategoryIconAttribute()
    {
        $icons = [
            'general' => 'fas fa-question-circle',
            'orders' => 'fas fa-shopping-cart',
            'shipping' => 'fas fa-truck',
            'returns' => 'fas fa-undo',
            'payments' => 'fas fa-credit-card',
            'products' => 'fas fa-tshirt'
        ];

        return $icons[$this->category] ?? 'fas fa-info-circle';
    }

    /**
     * Get helpfulness percentage
     */
    public function getHelpfulnessPercentageAttribute()
    {
        $total = $this->helpful_count + $this->not_helpful_count;
        if ($total === 0) return 0;
        
        return round(($this->helpful_count / $total) * 100);
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Mark as helpful
     */
    public function markAsHelpful()
    {
        $this->increment('helpful_count');
    }

    /**
     * Mark as not helpful
     */
    public function markAsNotHelpful()
    {
        $this->increment('not_helpful_count');
    }

    /**
     * Get related FAQs based on category and tags
     */
    public function getRelatedFaqs($limit = 5)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where(function($query) {
                $query->where('category', $this->category);
                
                if ($this->tags) {
                    foreach ($this->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->orderBy('helpful_count', 'desc')
            ->orderBy('view_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get all categories with counts
     */
    public static function getCategoriesWithCounts()
    {
        return static::published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => [
                    'count' => $item->count,
                    'display_name' => (new static(['category' => $item->category]))->category_display,
                    'icon' => (new static(['category' => $item->category]))->category_icon
                ]];
            });
    }

    /**
     * Get popular FAQs
     */
    public static function getPopular($limit = 10)
    {
        return static::published()
            ->orderBy('view_count', 'desc')
            ->orderBy('helpful_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Search FAQs with relevance scoring
     */
    public static function searchWithRelevance($searchTerm, $category = null)
    {
        $query = static::published();

        if ($category) {
            $query->where('category', $category);
        }

        return $query->where(function($q) use ($searchTerm) {
            // Full text search for better relevance
            $q->whereRaw("MATCH(question, answer) AGAINST(? IN NATURAL LANGUAGE MODE)", [$searchTerm])
              ->orWhere('question', 'LIKE', "%{$searchTerm}%")
              ->orWhere('answer', 'LIKE', "%{$searchTerm}%");
        })
        ->orderByRaw("
            CASE 
                WHEN question LIKE ? THEN 1
                WHEN question LIKE ? THEN 2
                WHEN answer LIKE ? THEN 3
                ELSE 4
            END
        ", ["%{$searchTerm}%", "{$searchTerm}%", "%{$searchTerm}%"])
        ->orderBy('helpful_count', 'desc')
        ->get();
    }
}
