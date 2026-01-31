<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * عرض جميع التقييمات مع الإحصائيات
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Review::with(['user', 'restaurant', 'order']);

        // فلاتر حسب الدور
        if ($user->hasRole('restaurant_owner')) {
            // يرى التقييمات الخاصة بمطعمه فقط
            $query->whereHas('restaurant', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif (!$user->hasRole('super_admin')) {
            abort(403);
        }

        // فلاتر متقدمة
        if ($request->has('restaurant_id') && $request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        if ($request->has('rating') && $request->rating) {
            $query->where('rating', $request->rating);
        }

        if ($request->has('is_verified') && $request->is_verified !== null) {
            $query->where('is_verified', $request->is_verified);
        }

        if ($request->has('is_approved') && $request->is_approved !== null) {
            $query->where('is_approved', $request->is_approved);
        }

        if ($request->has('status')) {
            $status = $request->status;
            if ($status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($status === 'published') {
                $query->where('is_approved', true);
            }
        }

        // فلتر البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reviewer_name', 'like', "%$search%")
                  ->orWhere('reviewer_email', 'like', "%$search%")
                  ->orWhere('comment', 'like', "%$search%");
            });
        }

        // فلتر النطاق الزمني
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // ترتيب
        $orderBy = $request->get('order_by', 'latest');
        if ($orderBy === 'rating_high') {
            $query->orderBy('rating', 'desc');
        } elseif ($orderBy === 'rating_low') {
            $query->orderBy('rating', 'asc');
        } elseif ($orderBy === 'helpful') {
            $query->orderBy('helpful_count', 'desc');
        } else {
            $query->latest();
        }

        // الإحصائيات
        $stats = $this->getReviewStats($user);
        $restaurants = Restaurant::where('user_id', $user->id)->get();

        $reviews = $query->paginate(20);

        return view('admin.reviews.index', compact('reviews', 'stats', 'restaurants'));
    }

    /**
     * عرض تفاصيل التقييم
     */
    public function show(Review $review)
    {
        $this->authorize('view', $review);

        $review->load(['user', 'restaurant', 'order']);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * الموافقة على التقييم
     */
    public function approve(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $review->is_approved = true;
        $review->save();

        // تنظيف الـ Cache
        Cache::forget("restaurant_reviews_stats_{$review->restaurant_id}");

        return back()->with('success', 'تم الموافقة على التقييم.');
    }

    /**
     * رفض التقييم
     */
    public function reject(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $review->is_approved = false;
        $review->is_public = false;
        $review->save();

        // حفظ سبب الرفض
        $review->notes = "السبب: " . $request->rejection_reason;
        $review->save();

        // تنظيف الـ Cache
        Cache::forget("restaurant_reviews_stats_{$review->restaurant_id}");

        return back()->with('success', 'تم رفض التقييم.');
    }

    /**
     * تمييز التقييم (Featured)
     */
    public function feature(Review $review)
    {
        $this->authorize('update', $review);

        $review->is_featured = !$review->is_featured;
        $review->save();

        Cache::forget("restaurant_reviews_stats_{$review->restaurant_id}");

        $message = $review->is_featured ? 'تم تمييز التقييم.' : 'تم إلغاء تمييز التقييم.';
        return back()->with('success', $message);
    }

    /**
     * إضافة رد من صاحب المطعم
     */
    public function addResponse(Request $request, Review $review)
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'owner_response' => 'required|string|max:1000',
        ]);

        $review->owner_response = $validated['owner_response'];
        $review->responded_at = Carbon::now();
        $review->save();

        Cache::forget("restaurant_reviews_stats_{$review->restaurant_id}");

        return back()->with('success', 'تم إضافة الرد بنجاح.');
    }

    /**
     * وضع علم (Report) على التقييم
     */
    public function report(Request $request, Review $review)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $review->report_count = ($review->report_count ?? 0) + 1;
        $review->save();

        // يمكن حفظ تفاصيل البلاغ في جدول منفصل

        return back()->with('success', 'تم تسجيل البلاغ.');
    }

    /**
     * حذف التقييم
     */
    public function destroy(Review $review)
    {
        $this->authorize('delete', $review);

        $restaurantId = $review->restaurant_id;
        $review->delete();

        Cache::forget("restaurant_reviews_stats_{$restaurantId}");

        return back()->with('success', 'تم حذف التقييم.');
    }

    /**
     * الحصول على إحصائيات التقييمات
     */
    private function getReviewStats($user)
    {
        $cacheKey = $user->hasRole('super_admin') ? 'reviews_stats_all' : "reviews_stats_user_{$user->id}";

        return Cache::remember($cacheKey, 3600, function () use ($user) {
            $query = Review::query();

            if ($user->hasRole('restaurant_owner')) {
                $query->whereHas('restaurant', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }

            $totalReviews = (clone $query)->count();
            $approvedReviews = (clone $query)->where('is_approved', true)->count();
            $pendingReviews = (clone $query)->where('is_approved', false)->count();
            $verifiedReviews = (clone $query)->where('is_verified', true)->count();

            // حساب متوسط التقييم
            $avgRating = (clone $query)->avg('rating') ?? 0;

            // توزيع التقييمات
            $ratingDistribution = [];
            for ($i = 5; $i >= 1; $i--) {
                $ratingDistribution[$i] = (clone $query)->where('rating', $i)->count();
            }

            // التقييمات الشهرية
            $now = Carbon::now();
            $thisMonthReviews = (clone $query)->whereMonth('created_at', $now->month)
                ->whereYear('created_at', $now->year)->count();

            // معدل الاستجابة
            $respondedReviews = (clone $query)->whereNotNull('owner_response')->count();
            $responseRate = $totalReviews > 0 ? ($respondedReviews / $totalReviews * 100) : 0;

            // التقييمات المفيدة
            $helpfulReviews = (clone $query)->where('is_helpful', true)->count();

            return [
                'total_reviews' => $totalReviews,
                'approved_reviews' => $approvedReviews,
                'pending_reviews' => $pendingReviews,
                'verified_reviews' => $verifiedReviews,
                'average_rating' => round($avgRating, 2),
                'rating_distribution' => $ratingDistribution,
                'this_month_reviews' => $thisMonthReviews,
                'response_rate' => round($responseRate, 2),
                'responded_reviews' => $respondedReviews,
                'helpful_reviews' => $helpfulReviews,
            ];
        });
    }

    /**
     * إحصائيات مفصلة لمطعم محدد
     */
    public function restaurantStats(Request $request, Restaurant $restaurant)
    {
        $this->authorize('view', $restaurant);

        $query = Review::where('restaurant_id', $restaurant->id);

        // الفترة الزمنية
        $period = $request->get('period', 'month'); // month, quarter, year
        $now = Carbon::now();

        if ($period === 'month') {
            $query->whereMonth('created_at', $now->month)
                  ->whereYear('created_at', $now->year);
        } elseif ($period === 'quarter') {
            $query->whereYear('created_at', $now->year)
                  ->whereBetween('created_at', [
                      $now->copy()->startOfQuarter(),
                      $now->copy()->endOfQuarter()
                  ]);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', $now->year);
        }

        $stats = [
            'total' => (clone $query)->count(),
            'average_rating' => (clone $query)->avg('rating') ?? 0,
            'five_star' => (clone $query)->where('rating', 5)->count(),
            'four_star' => (clone $query)->where('rating', 4)->count(),
            'three_star' => (clone $query)->where('rating', 3)->count(),
            'two_star' => (clone $query)->where('rating', 2)->count(),
            'one_star' => (clone $query)->where('rating', 1)->count(),
            'with_comment' => (clone $query)->whereNotNull('comment')->count(),
            'with_images' => (clone $query)->whereJsonLength('images', '>', 0)->count(),
            'verified' => (clone $query)->where('is_verified', true)->count(),
        ];

        return view('admin.reviews.restaurant_stats', compact('restaurant', 'stats', 'period'));
    }

    /**
     * تصدير التقييمات (CSV)
     */
    public function export(Request $request)
    {
        $user = auth()->user();

        $query = Review::with(['user', 'restaurant']);

        if ($user->hasRole('restaurant_owner')) {
            $query->whereHas('restaurant', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        } elseif (!$user->hasRole('super_admin')) {
            abort(403);
        }

        if ($request->has('restaurant_id') && $request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $reviews = $query->get();

        $csv = "المراجع,البريد,المطعم,التقييم,التعليق,مُتحقق,مُوافق عليه,التاريخ\n";

        foreach ($reviews as $review) {
            $comment = str_replace(',', '', $review->comment ?? '');
            $csv .= "{$review->reviewer_name},{$review->reviewer_email},{$review->restaurant->name}," .
                    "{$review->rating},{$comment}," .
                    ($review->is_verified ? 'نعم' : 'لا') . "," .
                    ($review->is_approved ? 'نعم' : 'لا') . "," .
                    "{$review->created_at}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="reviews.csv"',
        ]);
    }

    /**
     * تحديث حالة التحقق (Verified)
     */
    public function toggleVerified(Review $review)
    {
        $this->authorize('update', $review);

        $review->is_verified = !$review->is_verified;
        $review->save();

        Cache::forget("restaurant_reviews_stats_{$review->restaurant_id}");

        return back()->with('success', 'تم تحديث حالة التحقق.');
    }
}
