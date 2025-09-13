<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use App\Models\BrowsingHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RecommendationController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Track product view
     */
    public function trackView(Request $request)
    {
        $productId = $request->input('product_id');
        $interactionData = $request->input('interaction_data', []);
        
        $userId = Auth::id();
        $sessionId = Session::getId();

        $this->recommendationService->trackProductView(
            $productId, 
            $userId, 
            $sessionId, 
            $interactionData
        );

        return response()->json(['success' => true]);
    }

    /**
     * Get personalized recommendations
     */
    public function getRecommendations(Request $request)
    {
        $limit = $request->input('limit', 12);
        $excludeIds = $request->input('exclude', []);
        $type = $request->input('type', 'personalized'); // personalized, similar, trending

        $userId = Auth::id();
        $sessionId = Session::getId();

        switch ($type) {
            case 'similar':
                $productId = $request->input('product_id');
                if (!$productId) {
                    return response()->json(['error' => 'Product ID required for similar recommendations'], 400);
                }
                $product = \App\Models\Product::find($productId);
                $recommendations = $this->recommendationService->findSimilarProducts($product, $limit);
                break;

            case 'trending':
                $preferences = $userId ? Auth::user()->preferences : null;
                $recommendations = $this->recommendationService->getTrendingRecommendations($preferences, $limit);
                break;

            case 'personalized':
            default:
                if ($userId) {
                    $recommendations = $this->recommendationService->getRecommendationsForUser($userId, $limit, $excludeIds);
                } else {
                    $recommendations = $this->recommendationService->getRecommendationsForSession($sessionId, $limit, $excludeIds);
                }
                break;
        }

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'formatted_price' => number_format($product->price) . ' VNĐ',
                    'image' => $product->image ? (
                        filter_var($product->image, FILTER_VALIDATE_URL) 
                            ? $product->image 
                            : asset('storage/' . $product->image)
                    ) : null,
                    'category' => $product->category ? $product->category->name : null,
                    'style' => $product->style,
                    'material' => $product->material,
                    'fit' => $product->fit,
                    'available_sizes' => $product->sizes->where('stock_quantity', '>', 0)->pluck('size'),
                    'url' => route('products.show', $product->id)
                ];
            })
        ]);
    }

    /**
     * Get recently viewed products
     */
    public function getRecentlyViewed(Request $request)
    {
        $limit = $request->input('limit', 10);
        $userId = Auth::id();
        $sessionId = Session::getId();

        $recentlyViewed = $this->recommendationService->getRecentlyViewed($userId, $sessionId, $limit);

        return response()->json([
            'success' => true,
            'recently_viewed' => $recentlyViewed->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'formatted_price' => number_format($product->price) . ' VNĐ',
                    'image' => $product->image ? (
                        filter_var($product->image, FILTER_VALIDATE_URL) 
                            ? $product->image 
                            : asset('storage/' . $product->image)
                    ) : null,
                    'category' => $product->category ? $product->category->name : null,
                    'url' => route('products.show', $product->id)
                ];
            })
        ]);
    }

    /**
     * Get user's browsing statistics
     */
    public function getBrowsingStats()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $stats = $this->recommendationService->getUserBrowsingStats(Auth::id());

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get user's preference profile
     */
    public function getPreferenceProfile()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Authentication required'], 401);
        }

        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return response()->json([
                'success' => true,
                'profile' => [
                    'type' => 'new_user',
                    'message' => 'Chưa có đủ dữ liệu để phân tích'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'profile' => [
                'behavior_type' => $preferences->behavior_profile['type'],
                'price_sensitivity' => $preferences->price_sensitivity,
                'brand_loyalty' => $preferences->brand_loyalty,
                'style_consistency' => $preferences->style_consistency,
                'impulse_buying' => $preferences->impulse_buying,
                'preferred_categories' => $preferences->preferred_categories,
                'preferred_styles' => $preferences->preferred_styles,
                'preferred_materials' => $preferences->preferred_materials,
                'preferred_price_range' => $preferences->price_range,
                'total_products_viewed' => $preferences->total_products_viewed,
                'total_purchases' => $preferences->total_purchases,
                'conversion_rate' => $preferences->conversion_rate,
                'last_analyzed' => $preferences->last_analyzed_at ? $preferences->last_analyzed_at->diffForHumans() : null
            ]
        ]);
    }

    /**
     * Update view duration when user leaves product page
     */
    public function updateViewDuration(Request $request)
    {
        $productId = $request->input('product_id');
        $duration = $request->input('duration', 0);
        $userId = Auth::id();
        $sessionId = Session::getId();

        // Find the most recent browsing history record for this product
        $history = BrowsingHistory::where('product_id', $productId)
            ->where(function($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId);
                }
            })
            ->orderBy('viewed_at', 'desc')
            ->first();

        if ($history) {
            $history->update(['view_duration' => $duration]);
        }

        return response()->json(['success' => true]);
    }
}
