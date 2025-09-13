<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;
use App\Models\User;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display blog listing
     */
    public function index(Request $request)
    {
        $featured = BlogPost::getFeatured(3);
        $recent = BlogPost::getRecent(12);
        $categories = BlogCategory::active()->orderBy('sort_order')->get();
        $popularTags = BlogTag::active()->featured()->orderBy('usage_count', 'desc')->take(20)->get();
        $archive = BlogPost::getArchiveData();

        return view('blog.index', compact('featured', 'recent', 'categories', 'popularTags', 'archive'));
    }

    /**
     * Display specific blog post
     */
    public function show($slug)
    {
        $post = BlogPost::published()
            ->where('slug', $slug)
            ->with(['author', 'category'])
            ->firstOrFail();

        // Increment view count
        $post->incrementViewCount();

        // Get related posts
        $relatedPosts = $post->getRelatedPosts(4);

        // Get next and previous posts
        $nextPost = BlogPost::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at', 'asc')
            ->first();

        $previousPost = BlogPost::published()
            ->where('published_at', '<', $post->published_at)
            ->orderBy('published_at', 'desc')
            ->first();

        return view('blog.show', compact('post', 'relatedPosts', 'nextPost', 'previousPost'));
    }

    /**
     * Display posts by category
     */
    public function category($slug)
    {
        $category = BlogCategory::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = BlogPost::published()
            ->inCategory($category->id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.category', compact('category', 'posts'));
    }

    /**
     * Display posts by tag
     */
    public function tag($slug)
    {
        $tag = BlogTag::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $posts = BlogPost::published()
            ->withTag($tag->id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.tag', compact('tag', 'posts'));
    }

    /**
     * Display posts by author
     */
    public function author($id)
    {
        $author = User::findOrFail($id);

        $posts = BlogPost::published()
            ->byAuthor($id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.author', compact('author', 'posts'));
    }

    /**
     * Search blog posts
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $categoryId = $request->get('category');
        $tagId = $request->get('tag');
        $authorId = $request->get('author');

        if (strlen($query) < 2 && !$categoryId && !$tagId && !$authorId) {
            return redirect()->route('blog.index')
                ->with('error', 'Vui lòng nhập từ khóa tìm kiếm.');
        }

        $filters = array_filter([
            'category_id' => $categoryId,
            'tag_id' => $tagId,
            'author_id' => $authorId
        ]);

        $posts = BlogPost::search($query, $filters);
        $categories = BlogCategory::active()->get();
        $tags = BlogTag::active()->get();

        return view('blog.search', compact('posts', 'query', 'categories', 'tags', 'filters'));
    }

    /**
     * Like a blog post
     */
    public function like($slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $post->incrementLikeCount();

        return response()->json([
            'success' => true,
            'like_count' => $post->like_count,
            'message' => 'Đã thích bài viết!'
        ]);
    }

    /**
     * Share a blog post
     */
    public function share($slug)
    {
        $post = BlogPost::where('slug', $slug)->firstOrFail();
        $post->incrementShareCount();

        return response()->json([
            'success' => true,
            'share_count' => $post->share_count,
            'message' => 'Đã chia sẻ bài viết!'
        ]);
    }

    /**
     * Get recent posts for sidebar/widget
     */
    public function getRecent($limit = 5)
    {
        $posts = BlogPost::getRecent($limit);

        return response()->json([
            'success' => true,
            'posts' => $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'excerpt' => \Str::limit($post->excerpt, 100),
                    'url' => $post->url,
                    'featured_image_url' => $post->featured_image_url,
                    'published_at' => $post->published_at->format('d/m/Y'),
                    'author' => $post->author->name ?? 'Unknown'
                ];
            })
        ]);
    }

    /**
     * Get popular posts
     */
    public function getPopular($limit = 5)
    {
        $posts = BlogPost::getPopular($limit);

        return response()->json([
            'success' => true,
            'posts' => $posts
        ]);
    }

    /**
     * Get blog categories for navigation
     */
    public function getCategories()
    {
        $categories = BlogCategory::active()
            ->where('show_in_menu', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    /**
     * Get RSS feed
     */
    public function rss()
    {
        $posts = BlogPost::published()
            ->orderBy('published_at', 'desc')
            ->take(20)
            ->get();

        return response()
            ->view('blog.rss', compact('posts'))
            ->header('Content-Type', 'application/rss+xml');
    }
}
