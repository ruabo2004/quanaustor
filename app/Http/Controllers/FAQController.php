<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FAQController extends Controller
{
    /**
     * Display FAQ listing page
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $category = $request->get('category');

        // Get categories with counts
        $categories = FAQ::getCategoriesWithCounts();

        // Get featured FAQs
        $featuredFaqs = FAQ::published()->featured()->orderBy('sort_order')->take(6)->get();

        // Get FAQs based on search/filter
        if ($search) {
            $faqs = FAQ::searchWithRelevance($search, $category);
        } else {
            $query = FAQ::published();
            
            if ($category) {
                $query->category($category);
            }
            
            $faqs = $query->orderBy('sort_order')->orderBy('helpful_count', 'desc')->get();
        }

        // Get popular FAQs
        $popularFaqs = FAQ::getPopular(5);

        return view('support.faq.index', compact(
            'faqs', 
            'featuredFaqs', 
            'popularFaqs', 
            'categories', 
            'search', 
            'category'
        ));
    }

    /**
     * Show specific FAQ
     */
    public function show($id)
    {
        $faq = FAQ::published()->findOrFail($id);
        
        // Increment view count
        $faq->incrementViewCount();
        
        // Get related FAQs
        $relatedFaqs = $faq->getRelatedFaqs(4);
        
        return view('support.faq.show', compact('faq', 'relatedFaqs'));
    }

    /**
     * Search FAQs via AJAX
     */
    public function search(Request $request)
    {
        $search = $request->get('q', '');
        $category = $request->get('category');
        
        if (strlen($search) < 2) {
            return response()->json([
                'success' => true,
                'faqs' => [],
                'message' => 'Vui lòng nhập ít nhất 2 ký tự để tìm kiếm'
            ]);
        }

        $faqs = FAQ::searchWithRelevance($search, $category);

        return response()->json([
            'success' => true,
            'faqs' => $faqs->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => Str::limit(strip_tags($faq->answer), 200),
                    'category' => $faq->category_display,
                    'category_icon' => $faq->category_icon,
                    'view_count' => $faq->view_count,
                    'helpfulness' => $faq->helpfulness_percentage,
                    'url' => route('faq.show', $faq->id)
                ];
            })
        ]);
    }

    /**
     * Get FAQ suggestions for autocomplete
     */
    public function suggestions(Request $request)
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $suggestions = FAQ::published()
            ->where('question', 'LIKE', "%{$search}%")
            ->orderBy('view_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'text' => $faq->question,
                    'category' => $faq->category_display
                ];
            });

        return response()->json($suggestions);
    }

    /**
     * Mark FAQ as helpful
     */
    public function markHelpful($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->markAsHelpful();

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn phản hồi của bạn!',
            'helpful_count' => $faq->helpful_count,
            'helpfulness_percentage' => $faq->helpfulness_percentage
        ]);
    }

    /**
     * Mark FAQ as not helpful
     */
    public function markNotHelpful($id)
    {
        $faq = FAQ::findOrFail($id);
        $faq->markAsNotHelpful();

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn phản hồi của bạn! Chúng tôi sẽ cải thiện nội dung này.',
            'not_helpful_count' => $faq->not_helpful_count,
            'helpfulness_percentage' => $faq->helpfulness_percentage
        ]);
    }

    /**
     * Get popular FAQs for widget
     */
    public function popular()
    {
        $faqs = FAQ::getPopular(10);

        return response()->json([
            'success' => true,
            'faqs' => $faqs->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'category' => $faq->category_display,
                    'category_icon' => $faq->category_icon,
                    'view_count' => $faq->view_count,
                    'url' => route('faq.show', $faq->id)
                ];
            })
        ]);
    }

    /**
     * Get categories for filter
     */
    public function categories()
    {
        $categories = FAQ::getCategoriesWithCounts();

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }
}
