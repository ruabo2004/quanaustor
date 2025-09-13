<?php

namespace App\Services;

use App\Models\UserSession;
use App\Models\UserActivity;
use App\Models\PageView;
use App\Models\UserInteraction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Jenssegers\Agent\Agent;

class AnalyticsService
{
    protected $agent;
    
    public function __construct()
    {
        $this->agent = new Agent();
    }
    
    /**
     * Start or get current user session
     */
    public function startSession(Request $request)
    {
        $sessionId = session()->getId();
        $userId = auth()->id();
        $guestId = $this->getGuestId($request);
        
        // Check if session already exists
        $userSession = UserSession::where('session_id', $sessionId)->first();
        
        if (!$userSession) {
            $userSession = UserSession::create([
                'session_id' => $sessionId,
                'user_id' => $userId,
                'guest_id' => $guestId,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_type' => $this->getDeviceType(),
                'browser' => $this->agent->browser(),
                'platform' => $this->agent->platform(),
                'device_info' => $this->getDeviceInfo(),
                'country' => $this->getCountryFromIP($request->ip()),
                'timezone' => $this->getTimezoneFromRequest($request),
                'referrer_domain' => $this->getReferrerDomain($request),
                'referrer_url' => $request->header('referer'),
                'utm_source' => $request->get('utm_source'),
                'utm_medium' => $request->get('utm_medium'),
                'utm_campaign' => $request->get('utm_campaign'),
                'utm_term' => $request->get('utm_term'),
                'utm_content' => $request->get('utm_content'),
                'started_at' => now(),
                'last_activity_at' => now()
            ]);
        } else {
            // Update last activity
            $userSession->update([
                'last_activity_at' => now(),
                'user_id' => $userId // Update if user logs in mid-session
            ]);
        }
        
        return $userSession;
    }
    
    /**
     * Track page view
     */
    public function trackPageView(Request $request, $pageType = null, $contentId = null)
    {
        $session = $this->startSession($request);
        
        $pageView = PageView::create([
            'user_id' => auth()->id(),
            'session_id' => $session->session_id,
            'guest_id' => $session->guest_id,
            'url' => $request->fullUrl(),
            'page_title' => $this->getPageTitle($request),
            'page_type' => $pageType,
            'url_parameters' => $request->query(),
            'product_id' => $pageType === 'product' ? $contentId : null,
            'blog_post_id' => $pageType === 'blog' ? $contentId : null,
            'category_slug' => $this->getCategorySlug($request),
            'is_entry_page' => $this->isEntryPage($session),
            'referrer_url' => $request->header('referer'),
            'referrer_domain' => $this->getReferrerDomain($request),
            'viewport_size' => $this->getViewportSize($request),
            'device_type' => $session->device_type,
            'browser' => $session->browser,
            'platform' => $session->platform,
            'utm_source' => $request->get('utm_source') ?? $session->utm_source,
            'utm_medium' => $request->get('utm_medium') ?? $session->utm_medium,
            'utm_campaign' => $request->get('utm_campaign') ?? $session->utm_campaign,
            'utm_term' => $request->get('utm_term') ?? $session->utm_term,
            'utm_content' => $request->get('utm_content') ?? $session->utm_content
        ]);
        
        // Update session metrics
        $session->increment('page_views');
        
        return $pageView;
    }
    
    /**
     * Track user activity
     */
    public function trackActivity($action, $data = [])
    {
        $sessionId = session()->getId();
        
        return UserActivity::create(array_merge([
            'user_id' => auth()->id(),
            'session_id' => $sessionId,
            'guest_id' => $this->getGuestId(request()),
            'action' => $action,
            'url' => request()->fullUrl(),
            'page_title' => $this->getPageTitle(request()),
            'device_type' => $this->getDeviceType(),
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform()
        ], $data));
    }
    
    /**
     * Track user interaction
     */
    public function trackInteraction($type, $data = [])
    {
        $sessionId = session()->getId();
        
        $interaction = UserInteraction::create(array_merge([
            'user_id' => auth()->id(),
            'session_id' => $sessionId,
            'guest_id' => $this->getGuestId(request()),
            'type' => $type,
            'page_url' => request()->fullUrl(),
            'page_type' => $data['page_type'] ?? null,
            'viewport_size' => $this->getViewportSize(request()),
            'device_type' => $this->getDeviceType(),
            'browser' => $this->agent->browser(),
            'platform' => $this->agent->platform()
        ], $data));
        
        // Update session interaction count
        $session = UserSession::where('session_id', $sessionId)->first();
        if ($session) {
            $session->increment('interactions');
        }
        
        return $interaction;
    }
    
    /**
     * Track ecommerce events
     */
    public function trackEcommerce($action, $productId, $data = [])
    {
        return $this->trackActivity('ecommerce_' . $action, array_merge([
            'category' => 'ecommerce',
            'product_id' => $productId,
            'target_type' => 'product',
            'target_id' => $productId
        ], $data));
    }
    
    /**
     * Track conversion
     */
    public function trackConversion($type, $value = null, $orderId = null)
    {
        $sessionId = session()->getId();
        
        // Update session with conversion
        UserSession::where('session_id', $sessionId)->update([
            'is_converted' => true,
            'conversion_type' => $type,
            'conversion_value' => $value
        ]);
        
        // Track conversion activity
        return $this->trackActivity('conversion', [
            'category' => 'conversion',
            'label' => $type,
            'value' => $value,
            'order_id' => $orderId,
            'currency' => 'VND'
        ]);
    }
    
    /**
     * End user session
     */
    public function endSession($sessionId = null)
    {
        $sessionId = $sessionId ?? session()->getId();
        
        $session = UserSession::where('session_id', $sessionId)->first();
        if ($session) {
            $duration = now()->diffInSeconds($session->started_at);
            $bounceRate = $this->calculateBounceRate($session);
            
            $session->update([
                'ended_at' => now(),
                'duration_seconds' => $duration,
                'bounce_rate' => $bounceRate
            ]);
            
            // Mark last page view as exit page
            PageView::where('session_id', $sessionId)
                ->orderBy('created_at', 'desc')
                ->first()?->update(['is_exit_page' => true]);
        }
        
        return $session;
    }
    
    /**
     * Get analytics dashboard data
     */
    public function getDashboardData($dateRange = '7d')
    {
        $startDate = $this->getStartDate($dateRange);
        
        return [
            'overview' => $this->getOverviewMetrics($startDate),
            'traffic' => $this->getTrafficMetrics($startDate),
            'engagement' => $this->getEngagementMetrics($startDate),
            'conversions' => $this->getConversionMetrics($startDate),
            'devices' => $this->getDeviceMetrics($startDate),
            'locations' => $this->getLocationMetrics($startDate),
            'pages' => $this->getTopPages($startDate),
            'products' => $this->getTopProducts($startDate),
            'referrers' => $this->getTopReferrers($startDate),
            'searches' => $this->getTopSearches($startDate)
        ];
    }
    
    /**
     * Get user journey for specific user or session
     */
    public function getUserJourney($userId = null, $sessionId = null)
    {
        $query = PageView::query();
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        if ($sessionId) {
            $query->where('session_id', $sessionId);
        }
        
        $pageViews = $query->orderBy('created_at')->get();
        
        $journey = [];
        foreach ($pageViews as $view) {
            $journey[] = [
                'timestamp' => $view->created_at,
                'page_title' => $view->page_title,
                'url' => $view->url,
                'page_type' => $view->page_type,
                'time_on_page' => $view->time_on_page,
                'scroll_depth' => $view->scroll_depth,
                'interactions' => $view->interactions,
                'is_entry' => $view->is_entry_page,
                'is_exit' => $view->is_exit_page
            ];
        }
        
        return $journey;
    }
    
    // Helper methods
    
    protected function getGuestId(Request $request)
    {
        $guestId = $request->cookie('guest_id');
        if (!$guestId) {
            $guestId = 'guest_' . Str::random(32);
            cookie()->queue('guest_id', $guestId, 60 * 24 * 365); // 1 year
        }
        return $guestId;
    }
    
    protected function getDeviceType()
    {
        if ($this->agent->isMobile()) return 'mobile';
        if ($this->agent->isTablet()) return 'tablet';
        return 'desktop';
    }
    
    protected function getDeviceInfo()
    {
        return [
            'device' => $this->agent->device(),
            'browser_version' => $this->agent->version($this->agent->browser()),
            'platform_version' => $this->agent->version($this->agent->platform()),
            'is_robot' => $this->agent->isRobot(),
            'languages' => $this->agent->languages()
        ];
    }
    
    protected function getCountryFromIP($ip)
    {
        // In production, use a GeoIP service like MaxMind
        return 'VN'; // Default to Vietnam
    }
    
    protected function getTimezoneFromRequest(Request $request)
    {
        return $request->header('X-Timezone') ?? 'Asia/Ho_Chi_Minh';
    }
    
    protected function getReferrerDomain(Request $request)
    {
        $referrer = $request->header('referer');
        if ($referrer) {
            return parse_url($referrer, PHP_URL_HOST);
        }
        return null;
    }
    
    protected function getPageTitle(Request $request)
    {
        return $request->header('X-Page-Title') ?? 'Unknown';
    }
    
    protected function getCategorySlug(Request $request)
    {
        $path = $request->path();
        if (strpos($path, 'categories/') === 0) {
            return substr($path, 11);
        }
        return null;
    }
    
    protected function isEntryPage($session)
    {
        return $session->page_views == 0;
    }
    
    protected function getViewportSize(Request $request)
    {
        return [
            'width' => $request->header('X-Viewport-Width'),
            'height' => $request->header('X-Viewport-Height')
        ];
    }
    
    protected function calculateBounceRate($session)
    {
        return $session->page_views <= 1 ? 100 : 0;
    }
    
    protected function getStartDate($dateRange)
    {
        switch ($dateRange) {
            case '1d': return now()->subDay();
            case '7d': return now()->subDays(7);
            case '30d': return now()->subDays(30);
            case '90d': return now()->subDays(90);
            default: return now()->subDays(7);
        }
    }
    
    protected function getOverviewMetrics($startDate)
    {
        return [
            'total_sessions' => UserSession::where('started_at', '>=', $startDate)->count(),
            'total_users' => UserSession::where('started_at', '>=', $startDate)->distinct('user_id')->count('user_id'),
            'total_page_views' => PageView::where('created_at', '>=', $startDate)->count(),
            'avg_session_duration' => UserSession::where('started_at', '>=', $startDate)->avg('duration_seconds'),
            'bounce_rate' => UserSession::where('started_at', '>=', $startDate)->avg('bounce_rate'),
            'conversion_rate' => UserSession::where('started_at', '>=', $startDate)->where('is_converted', true)->count() / 
                               max(1, UserSession::where('started_at', '>=', $startDate)->count()) * 100
        ];
    }
    
    protected function getTrafficMetrics($startDate)
    {
        return [
            'by_hour' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('HOUR(started_at) as hour, COUNT(*) as sessions')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
            'by_day' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('DATE(started_at) as date, COUNT(*) as sessions')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'by_source' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('COALESCE(utm_source, referrer_domain, "direct") as source, COUNT(*) as sessions')
                ->groupBy('source')
                ->orderBy('sessions', 'desc')
                ->take(10)
                ->get()
        ];
    }
    
    protected function getEngagementMetrics($startDate)
    {
        return [
            'avg_time_on_page' => PageView::where('created_at', '>=', $startDate)->avg('time_on_page'),
            'avg_scroll_depth' => PageView::where('created_at', '>=', $startDate)->avg('scroll_depth'),
            'total_interactions' => UserInteraction::where('created_at', '>=', $startDate)->count(),
            'most_clicked_elements' => UserInteraction::where('created_at', '>=', $startDate)
                ->where('type', 'click')
                ->selectRaw('element_text, COUNT(*) as clicks')
                ->groupBy('element_text')
                ->orderBy('clicks', 'desc')
                ->take(10)
                ->get()
        ];
    }
    
    protected function getConversionMetrics($startDate)
    {
        return [
            'total_conversions' => UserSession::where('started_at', '>=', $startDate)->where('is_converted', true)->count(),
            'conversion_value' => UserSession::where('started_at', '>=', $startDate)->sum('conversion_value'),
            'by_type' => UserSession::where('started_at', '>=', $startDate)
                ->where('is_converted', true)
                ->selectRaw('conversion_type, COUNT(*) as conversions, SUM(conversion_value) as value')
                ->groupBy('conversion_type')
                ->get()
        ];
    }
    
    protected function getDeviceMetrics($startDate)
    {
        return [
            'by_type' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('device_type, COUNT(*) as sessions')
                ->groupBy('device_type')
                ->get(),
            'by_browser' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('browser, COUNT(*) as sessions')
                ->groupBy('browser')
                ->orderBy('sessions', 'desc')
                ->take(10)
                ->get(),
            'by_platform' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('platform, COUNT(*) as sessions')
                ->groupBy('platform')
                ->orderBy('sessions', 'desc')
                ->take(10)
                ->get()
        ];
    }
    
    protected function getLocationMetrics($startDate)
    {
        return [
            'by_country' => UserSession::where('started_at', '>=', $startDate)
                ->selectRaw('country, COUNT(*) as sessions')
                ->groupBy('country')
                ->orderBy('sessions', 'desc')
                ->take(10)
                ->get()
        ];
    }
    
    protected function getTopPages($startDate)
    {
        return PageView::where('created_at', '>=', $startDate)
            ->selectRaw('page_title, COUNT(*) as views, AVG(time_on_page) as avg_time')
            ->groupBy('page_title')
            ->orderBy('views', 'desc')
            ->take(20)
            ->get();
    }
    
    protected function getTopProducts($startDate)
    {
        return PageView::where('created_at', '>=', $startDate)
            ->whereNotNull('product_id')
            ->selectRaw('product_id, COUNT(*) as views')
            ->groupBy('product_id')
            ->orderBy('views', 'desc')
            ->take(20)
            ->get();
    }
    
    protected function getTopReferrers($startDate)
    {
        return UserSession::where('started_at', '>=', $startDate)
            ->whereNotNull('referrer_domain')
            ->selectRaw('referrer_domain, COUNT(*) as sessions')
            ->groupBy('referrer_domain')
            ->orderBy('sessions', 'desc')
            ->take(20)
            ->get();
    }
    
    protected function getTopSearches($startDate)
    {
        return UserInteraction::where('created_at', '>=', $startDate)
            ->where('type', 'search')
            ->whereNotNull('search_query')
            ->selectRaw('search_query, COUNT(*) as searches')
            ->groupBy('search_query')
            ->orderBy('searches', 'desc')
            ->take(20)
            ->get();
    }
}
