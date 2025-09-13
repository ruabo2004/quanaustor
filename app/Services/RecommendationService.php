<?php

namespace App\Services;

use App\Models\Product;
use App\Models\BrowsingHistory;
use App\Models\UserPreference;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class RecommendationService
{
    /**
     * Get personalized product recommendations for a user
     */
    public function getRecommendationsForUser($userId, $limit = 12, $excludeIds = [])
    {
        $user = User::find($userId);
        if (!$user) {
            return $this->getFallbackRecommendations($limit, $excludeIds);
        }

        // Get user preferences
        $preferences = $user->preferences ?? $this->createInitialPreferences($user);
        
        // Get user's browsing history
        $browsingHistory = BrowsingHistory::forUser($userId)
            ->with('product.category')
            ->recent(90)
            ->get();

        // Get user's purchase history
        $purchaseHistory = $this->getUserPurchaseHistory($userId);

        // Generate recommendations using multiple algorithms
        $recommendations = collect();

        // 1. Content-based filtering (40% weight)
        $contentBased = $this->getContentBasedRecommendations($preferences, $browsingHistory, $limit * 0.4);
        $recommendations = $recommendations->merge($contentBased);

        // 2. Collaborative filtering (30% weight)
        $collaborative = $this->getCollaborativeRecommendations($userId, $purchaseHistory, $limit * 0.3);
        $recommendations = $recommendations->merge($collaborative);

        // 3. Similar products based on browsing (20% weight)
        $similar = $this->getSimilarProductRecommendations($browsingHistory, $limit * 0.2);
        $recommendations = $recommendations->merge($similar);

        // 4. Trending/Popular products (10% weight)
        $trending = $this->getTrendingRecommendations($preferences, $limit * 0.1);
        $recommendations = $recommendations->merge($trending);

        // Remove duplicates and excluded products
        $recommendations = $recommendations
            ->unique('id')
            ->whereNotIn('id', $excludeIds)
            ->take($limit);

        // If not enough recommendations, fill with fallback
        if ($recommendations->count() < $limit) {
            $needed = $limit - $recommendations->count();
            $fallback = $this->getFallbackRecommendations($needed, 
                array_merge($excludeIds, $recommendations->pluck('id')->toArray()));
            $recommendations = $recommendations->merge($fallback);
        }

        return $recommendations->take($limit);
    }

    /**
     * Get recommendations for guest users (session-based)
     */
    public function getRecommendationsForSession($sessionId, $limit = 12, $excludeIds = [])
    {
        // Get session browsing history
        $browsingHistory = BrowsingHistory::forSession($sessionId)
            ->with('product.category')
            ->recent(30)
            ->get();

        if ($browsingHistory->isEmpty()) {
            return $this->getFallbackRecommendations($limit, $excludeIds);
        }

        $recommendations = collect();

        // 1. Similar products to viewed (60% weight)
        $similar = $this->getSimilarProductRecommendations($browsingHistory, $limit * 0.6);
        $recommendations = $recommendations->merge($similar);

        // 2. Popular in same categories (40% weight)
        $categoryIds = $browsingHistory->pluck('product.category_id')->unique()->filter();
        $popular = $this->getPopularInCategories($categoryIds, $limit * 0.4);
        $recommendations = $recommendations->merge($popular);

        // Remove duplicates and excluded products
        $recommendations = $recommendations
            ->unique('id')
            ->whereNotIn('id', $excludeIds)
            ->take($limit);

        // Fill with fallback if needed
        if ($recommendations->count() < $limit) {
            $needed = $limit - $recommendations->count();
            $fallback = $this->getFallbackRecommendations($needed, 
                array_merge($excludeIds, $recommendations->pluck('id')->toArray()));
            $recommendations = $recommendations->merge($fallback);
        }

        return $recommendations->take($limit);
    }

    /**
     * Content-based recommendations based on user preferences
     */
    private function getContentBasedRecommendations($preferences, $browsingHistory, $limit)
    {
        $query = Product::with(['category', 'sizes']);

        // Filter by preferred categories
        if ($preferences->preferred_categories) {
            $query->whereIn('category_id', $preferences->preferred_categories);
        }

        // Filter by preferred styles
        if ($preferences->preferred_styles) {
            $query->whereIn('style', $preferences->preferred_styles);
        }

        // Filter by preferred materials
        if ($preferences->preferred_materials) {
            $query->whereIn('material', $preferences->preferred_materials);
        }

        // Filter by price range
        if ($preferences->preferred_min_price) {
            $query->where('price', '>=', $preferences->preferred_min_price);
        }
        if ($preferences->preferred_max_price) {
            $query->where('price', '<=', $preferences->preferred_max_price);
        }

        // Exclude already viewed products from recent history
        $recentlyViewed = $browsingHistory->pluck('product_id')->toArray();
        if (!empty($recentlyViewed)) {
            $query->whereNotIn('id', $recentlyViewed);
        }

        // Order by relevance score
        return $query->inRandomOrder()->take($limit)->get();
    }

    /**
     * Collaborative filtering - users who bought similar items
     */
    private function getCollaborativeRecommendations($userId, $purchaseHistory, $limit)
    {
        if (empty($purchaseHistory)) {
            return collect();
        }

        // Find users who bought similar products
        $similarUsers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('order_items.product_id', $purchaseHistory)
            ->where('orders.user_id', '!=', $userId)
            ->where('orders.payment_status', 'paid')
            ->groupBy('orders.user_id')
            ->havingRaw('COUNT(DISTINCT order_items.product_id) >= ?', [min(2, count($purchaseHistory) * 0.3)])
            ->pluck('orders.user_id')
            ->take(50);

        if ($similarUsers->isEmpty()) {
            return collect();
        }

        // Get products bought by similar users
        $recommendedProductIds = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.user_id', $similarUsers)
            ->whereNotIn('order_items.product_id', $purchaseHistory)
            ->where('orders.payment_status', 'paid')
            ->groupBy('order_items.product_id')
            ->orderByRaw('COUNT(*) DESC')
            ->take($limit)
            ->pluck('order_items.product_id');

        return Product::with(['category', 'sizes'])
            ->whereIn('id', $recommendedProductIds)
            ->get();
    }

    /**
     * Get similar products based on product attributes
     */
    private function getSimilarProductRecommendations($browsingHistory, $limit)
    {
        if ($browsingHistory->isEmpty()) {
            return collect();
        }

        $viewedProducts = $browsingHistory->pluck('product')->filter();
        $recommendations = collect();

        foreach ($viewedProducts->take(5) as $product) {
            $similar = $this->findSimilarProducts($product, 3);
            $recommendations = $recommendations->merge($similar);
        }

        return $recommendations->unique('id')->take($limit);
    }

    /**
     * Find products similar to a given product
     */
    public function findSimilarProducts($product, $limit = 5)
    {
        $query = Product::with(['category', 'sizes'])
            ->where('id', '!=', $product->id);

        // Calculate similarity score
        $query->selectRaw('products.*, 
            (CASE WHEN category_id = ? THEN 3 ELSE 0 END +
             CASE WHEN style = ? THEN 2 ELSE 0 END +
             CASE WHEN material = ? THEN 2 ELSE 0 END +
             CASE WHEN fit = ? THEN 1 ELSE 0 END +
             CASE WHEN ABS(price - ?) < ? THEN 2 ELSE 0 END) as similarity_score',
            [
                $product->category_id,
                $product->style,
                $product->material,
                $product->fit,
                $product->price,
                $product->price * 0.3 // 30% price tolerance
            ]
        );

        return $query->having('similarity_score', '>', 3)
            ->orderBy('similarity_score', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get trending/popular products
     */
    private function getTrendingRecommendations($preferences, $limit)
    {
        $query = Product::with(['category', 'sizes'])
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('browsing_histories', 'products.id', '=', 'browsing_histories.product_id')
            ->select('products.*')
            ->selectRaw('
                (COALESCE(SUM(order_items.quantity), 0) * 2 + 
                 COUNT(DISTINCT browsing_histories.id)) as popularity_score
            ')
            ->where(function($q) {
                $q->where('orders.payment_status', 'paid')
                  ->orWhere('orders.payment_status', null);
            })
            ->where('browsing_histories.viewed_at', '>=', now()->subDays(30))
            ->groupBy('products.id');

        // Apply preference filters if available
        if ($preferences && $preferences->preferred_categories) {
            $query->whereIn('products.category_id', $preferences->preferred_categories);
        }

        return $query->orderBy('popularity_score', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Get popular products in specific categories
     */
    private function getPopularInCategories($categoryIds, $limit)
    {
        if ($categoryIds->isEmpty()) {
            return collect();
        }

        return Product::with(['category', 'sizes'])
            ->whereIn('category_id', $categoryIds)
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('products.*')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as sales_count')
            ->groupBy('products.id')
            ->orderBy('sales_count', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Fallback recommendations when no personalization is available
     */
    private function getFallbackRecommendations($limit, $excludeIds = [])
    {
        return Product::with(['category', 'sizes'])
            ->whereNotIn('id', $excludeIds)
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->select('products.*')
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as sales_count')
            ->groupBy('products.id')
            ->orderBy('sales_count', 'desc')
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Track product view for personalization
     */
    public function trackProductView($productId, $userId = null, $sessionId = null, $interactionData = [])
    {
        $data = [
            'product_id' => $productId,
            'user_id' => $userId,
            'session_id' => $sessionId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'interaction_data' => $interactionData,
            'viewed_at' => now()
        ];

        // Create browsing history record
        BrowsingHistory::create($data);

        // Update user preferences if user is logged in
        if ($userId) {
            $this->updateUserPreferences($userId, $productId);
        }
    }

    /**
     * Update user preferences based on product interaction
     */
    private function updateUserPreferences($userId, $productId)
    {
        $product = Product::with('category')->find($productId);
        if (!$product) return;

        $preferences = UserPreference::firstOrCreate(['user_id' => $userId]);

        // Prepare behavior data
        $behaviorData = [
            'products_viewed' => 1,
            'categories' => [$product->category_id],
            'styles' => $product->style ? [$product->style] : [],
            'materials' => $product->material ? [$product->material] : [],
            'fits' => $product->fit ? [$product->fit] : []
        ];

        $preferences->updateFromBehavior($behaviorData);
    }

    /**
     * Get user's purchase history (product IDs)
     */
    private function getUserPurchaseHistory($userId)
    {
        return DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.user_id', $userId)
            ->where('orders.payment_status', 'paid')
            ->pluck('order_items.product_id')
            ->unique()
            ->toArray();
    }

    /**
     * Create initial preferences for new user
     */
    private function createInitialPreferences($user)
    {
        return UserPreference::create([
            'user_id' => $user->id,
            'price_sensitivity' => 0.5,
            'brand_loyalty' => 0.5,
            'style_consistency' => 0.5,
            'impulse_buying' => 0.5
        ]);
    }

    /**
     * Get recently viewed products for user
     */
    public function getRecentlyViewed($userId = null, $sessionId = null, $limit = 10)
    {
        $query = BrowsingHistory::with('product.category')
            ->forUserOrSession($userId, $sessionId)
            ->recent(30)
            ->orderBy('viewed_at', 'desc')
            ->take($limit);

        return $query->get()->pluck('product')->filter()->unique('id');
    }

    /**
     * Get user's browsing statistics
     */
    public function getUserBrowsingStats($userId)
    {
        $stats = BrowsingHistory::forUser($userId)
            ->recent(30)
            ->with('product.category')
            ->get();

        return [
            'total_views' => $stats->count(),
            'unique_products' => $stats->unique('product_id')->count(),
            'categories_viewed' => $stats->pluck('product.category.name')->unique()->filter()->values(),
            'average_session_time' => $stats->avg('view_duration'),
            'most_viewed_day' => $stats->groupBy(function($item) {
                return $item->viewed_at->format('l');
            })->sortByDesc('count')->keys()->first()
        ];
    }
}
