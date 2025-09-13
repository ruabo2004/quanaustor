<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon', 'image',
        'meta_title', 'meta_description', 'parent_id', 'sort_order',
        'is_active', 'show_in_menu', 'post_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_in_menu' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }

    public function publishedPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class, 'category_id')->published();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getUrlAttribute()
    {
        return route('blog.category', $this->slug);
    }

    public function updatePostCount()
    {
        $this->update(['post_count' => $this->publishedPosts()->count()]);
    }
}
