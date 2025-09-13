<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'gallery',
        'category_id',
        'tags',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'twitter_card',
        'status',
        'published_at',
        'scheduled_at',
        'is_featured',
        'allow_comments',
        'reading_time',
        'language',
        'related_posts',
        'view_count',
        'like_count',
        'share_count',
        'comment_count',
        'avg_rating',
        'last_viewed_at',
        'author_id',
        'updated_by'
    ];

    protected $casts = [
        'gallery' => 'array',
        'tags' => 'array',
        'meta_keywords' => 'array',
        'related_posts' => 'array',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'last_viewed_at' => 'datetime',
        'avg_rating' => 'decimal:2'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if (empty($post->meta_title)) {
                $post->meta_title = $post->title;
            }
            if (empty($post->reading_time)) {
                $post->reading_time = static::calculateReadingTime($post->content);
            }
        });

        static::updating(function ($post) {
            if ($post->isDirty('title') && empty($post->slug)) {
                $post->slug = Str::slug($post->title);
            }
            if ($post->isDirty('content')) {
                $post->reading_time = static::calculateReadingTime($post->content);
            }
        });
    }

    /**
     * Get the author of the post
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * Get the updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Scope for published posts
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where(function($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }

    /**
     * Scope for featured posts
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->published();
    }

    /**
     * Scope for category
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for tag
     */
    public function scopeWithTag($query, $tagId)
    {
        return $query->whereJsonContains('tags', $tagId);
    }

    /**
     * Scope for author
     */
    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    /**
     * Get post URL
     */
    public function getUrlAttribute()
    {
        return route('blog.show', $this->slug);
    }

    /**
     * Get featured image URL
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    /**
     * Get status display
     */
    public function getStatusDisplayAttribute()
    {
        $statuses = [
            'draft' => 'Bản nháp',
            'published' => 'Đã xuất bản',
            'scheduled' => 'Đã lên lịch',
            'archived' => 'Đã lưu trữ'
        ];

        return $statuses[$this->status] ?? 'Không xác định';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'secondary',
            'published' => 'success',
            'scheduled' => 'warning',
            'archived' => 'dark'
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    /**
     * Check if post is published
     */
    public function getIsPublishedAttribute()
    {
        return $this->status === 'published' && 
               ($this->published_at === null || $this->published_at <= now());
    }

    /**
     * Check if post is scheduled
     */
    public function getIsScheduledAttribute()
    {
        return $this->status === 'scheduled' || 
               ($this->published_at && $this->published_at > now());
    }

    /**
     * Get related posts
     */
    public function getRelatedPosts($limit = 5)
    {
        // First, try manual related posts
        if ($this->related_posts) {
            $related = static::published()
                ->whereIn('id', $this->related_posts)
                ->take($limit)
                ->get();
            
            if ($related->count() >= $limit) {
                return $related;
            }
        }

        // Auto-generate related posts based on category and tags
        $query = static::published()
            ->where('id', '!=', $this->id);

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        // Add tag-based filtering if tags exist
        if ($this->tags) {
            $query->orWhere(function($q) {
                foreach ($this->tags as $tag) {
                    $q->orWhereJsonContains('tags', $tag);
                }
            });
        }

        return $query->orderBy('view_count', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->take($limit)
                    ->get();
    }

    /**
     * Get tag objects from IDs
     */
    public function getTagObjectsAttribute()
    {
        if (!$this->tags) return collect();
        
        return BlogTag::whereIn('id', $this->tags)->get();
    }

    /**
     * Increment view count
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
        $this->update(['last_viewed_at' => now()]);
    }

    /**
     * Increment like count
     */
    public function incrementLikeCount()
    {
        $this->increment('like_count');
    }

    /**
     * Increment share count
     */
    public function incrementShareCount()
    {
        $this->increment('share_count');
    }

    /**
     * Calculate reading time
     */
    public static function calculateReadingTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, round($wordCount / 200)); // Average 200 words per minute
    }

    /**
     * Search posts
     */
    public static function search($query, $filters = [])
    {
        $posts = static::published()
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            });

        // Apply filters
        if (isset($filters['category_id'])) {
            $posts->where('category_id', $filters['category_id']);
        }

        if (isset($filters['author_id'])) {
            $posts->where('author_id', $filters['author_id']);
        }

        if (isset($filters['tag_id'])) {
            $posts->whereJsonContains('tags', $filters['tag_id']);
        }

        return $posts->orderBy('published_at', 'desc')->get();
    }

    /**
     * Get popular posts
     */
    public static function getPopular($limit = 10, $days = 30)
    {
        return static::published()
            ->where('published_at', '>=', now()->subDays($days))
            ->orderBy('view_count', 'desc')
            ->orderBy('like_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get recent posts
     */
    public static function getRecent($limit = 10)
    {
        return static::published()
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get featured posts
     */
    public static function getFeatured($limit = 5)
    {
        return static::featured()
            ->orderBy('published_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get posts by category
     */
    public static function getByCategory($categoryId, $limit = null)
    {
        $query = static::published()->inCategory($categoryId);
        
        if ($limit) {
            $query->take($limit);
        }
        
        return $query->orderBy('published_at', 'desc')->get();
    }

    /**
     * Get archive data (years and months with post counts)
     */
    public static function getArchiveData()
    {
        return static::published()
            ->selectRaw('YEAR(published_at) as year, MONTH(published_at) as month, COUNT(*) as count')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy('year');
    }
}
