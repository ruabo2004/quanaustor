<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display a specific page
     */
    public function show($slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Check if user can view this page
        if (!$page->isViewableBy(auth()->user())) {
            if ($page->require_auth && !auth()->check()) {
                return redirect()->route('login')
                    ->with('message', 'Bạn cần đăng nhập để xem trang này.');
            }
            
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Increment view count
        $page->incrementViewCount();

        // Get related pages
        $relatedPages = $page->getRelatedPages(4);

        // Determine view template
        $template = $page->template === 'default' ? 'pages.show' : "pages.templates.{$page->template}";
        
        // Check if custom template exists, fallback to default
        if (!view()->exists($template)) {
            $template = 'pages.show';
        }

        return view($template, compact('page', 'relatedPages'));
    }

    /**
     * Get menu pages for navigation
     */
    public function getMenuPages()
    {
        return Page::menu()->get();
    }

    /**
     * Search pages
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập ít nhất 2 ký tự để tìm kiếm'
            ]);
        }

        $pages = Page::search($query);

        return response()->json([
            'success' => true,
            'pages' => $pages->map(function ($page) {
                return [
                    'id' => $page->id,
                    'title' => $page->title,
                    'excerpt' => $page->excerpt_display,
                    'url' => $page->url,
                    'view_count' => $page->view_count
                ];
            })
        ]);
    }

    /**
     * Get popular pages
     */
    public function popular()
    {
        $pages = Page::getPopular(10);

        return response()->json([
            'success' => true,
            'pages' => $pages
        ]);
    }
}
