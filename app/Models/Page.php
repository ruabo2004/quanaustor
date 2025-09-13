<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'template',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'featured_image',
        'gallery',
        'is_published',
        'is_featured',
        'published_at',
        'show_in_menu',
        'menu_order',
        'parent_page',
        'custom_fields',
        'require_auth',
        'allowed_roles',
        'view_count',
        'last_viewed_at',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'meta_keywords' => 'array',
        'gallery' => 'array',
        'custom_fields' => 'array',
        'allowed_roles' => 'array',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'show_in_menu' => 'boolean',
        'require_auth' => 'boolean',
        'published_at' => 'datetime',
        'last_viewed_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
            if (empty($page->meta_title)) {
                $page->meta_title = $page->title;
            }
        });

        static::updating(function ($page) {
            if ($page->isDirty('title') && empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    /**
     * Get the author of the page
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the last updater
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope for published pages
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where(function($q) {
                        $q->whereNull('published_at')
                          ->orWhere('published_at', '<=', now());
                    });
    }

    /**
     * Scope for menu pages
     */
    public function scopeMenu($query)
    {
        return $query->where('show_in_menu', true)
                    ->where('is_published', true)
                    ->orderBy('menu_order');
    }

    /**
     * Scope for featured pages
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->published();
    }

    /**
     * Get page URL
     */
    public function getUrlAttribute()
    {
        return route('pages.show', $this->slug);
    }

    /**
     * Get featured image URL
     */
    public function getFeaturedImageUrlAttribute()
    {
        return $this->featured_image ? asset('storage/' . $this->featured_image) : null;
    }

    /**
     * Get page excerpt or auto-generated one
     */
    public function getExcerptDisplayAttribute()
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }
        
        return Str::limit(strip_tags($this->content), 160);
    }

    /**
     * Get reading time estimate
     */
    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        return max(1, round($wordCount / 200)); // Average 200 words per minute
    }

    /**
     * Check if page is viewable by current user
     */
    public function isViewableBy($user = null)
    {
        if (!$this->is_published) {
            return false;
        }

        if (!$this->require_auth) {
            return true;
        }

        if (!$user) {
            return false;
        }

        if (!$this->allowed_roles) {
            return true; // Any authenticated user
        }

        return in_array($user->role, $this->allowed_roles);
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
     * Get related pages
     */
    public function getRelatedPages($limit = 5)
    {
        return static::published()
            ->where('id', '!=', $this->id)
            ->where('template', $this->template)
            ->orderBy('view_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Generate breadcrumbs
     */
    public function getBreadcrumbsAttribute()
    {
        $breadcrumbs = [
            ['title' => 'Trang chủ', 'url' => route('home')]
        ];

        if ($this->parent_page) {
            $parent = static::where('slug', $this->parent_page)->first();
            if ($parent) {
                $breadcrumbs[] = ['title' => $parent->title, 'url' => $parent->url];
            }
        }

        $breadcrumbs[] = ['title' => $this->title, 'url' => null];

        return $breadcrumbs;
    }

    /**
     * Get all templates
     */
    public static function getAvailableTemplates()
    {
        return [
            'default' => 'Mặc định',
            'full-width' => 'Toàn bộ chiều rộng',
            'sidebar' => 'Có sidebar',
            'landing' => 'Landing page',
            'contact' => 'Liên hệ',
            'about' => 'Về chúng tôi',
            'privacy' => 'Chính sách bảo mật',
            'terms' => 'Điều khoản sử dụng'
        ];
    }

    /**
     * Get template display name
     */
    public function getTemplateDisplayAttribute()
    {
        $templates = static::getAvailableTemplates();
        return $templates[$this->template] ?? 'Mặc định';
    }

    /**
     * Search pages
     */
    public static function search($query)
    {
        return static::published()
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhere('excerpt', 'LIKE', "%{$query}%");
            })
            ->orderBy('view_count', 'desc')
            ->get();
    }

    /**
     * Get popular pages
     */
    public static function getPopular($limit = 10)
    {
        return static::published()
            ->orderBy('view_count', 'desc')
            ->take($limit)
            ->get();
    }
}
