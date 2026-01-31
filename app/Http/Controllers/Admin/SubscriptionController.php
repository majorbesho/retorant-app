<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class SubscriptionController extends Controller
{
    /**
     * عرض جميع الاشتراكات مع الإحصائيات
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // تحديد Query الأساسية
        $query = UserSubscription::with(['user', 'subscriptionPlan']);

        // فلاتر حسب الدور
        if ($user->hasRole('super_admin')) {
            // Super Admin يرى كل الاشتراكات ومواعيد انتهائها
            // لا يوجد تقييد
        } elseif ($user->hasRole('restaurant_owner')) {
            // يرى الاشتراكات الخاصة به فقط
            $query->where('user_id', $user->id);
        } else {
            // لا يوجد وصول
            abort(403);
        }

        // فلاتر متقدمة
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('plan_id') && $request->plan_id) {
            $query->where('subscription_plan_id', $request->plan_id);
        }

        if ($request->has('user_id') && $request->user_id && $user->hasRole('super_admin')) {
            $query->where('user_id', $request->user_id);
        }

        // فلتر النطاق الزمني
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // فلتر البحث
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        // الإحصائيات المخزنة مؤقتاً
        $stats = $this->getSubscriptionStats();
        $plans = SubscriptionPlan::active()->get();

        $subscriptions = $query->latest()->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions', 'stats', 'plans'));
    }

    /**
     * عرض تفاصيل اشتراك محدد
     */
    public function show(UserSubscription $subscription)
    {
        $this->authorize('view', $subscription);

        // معلومات تفصيلية
        $subscription->load([
            'user',
            'subscriptionPlan',
            'paymentMethod',
            'restaurants'
        ]);

        // سجل الفواتير
        $invoices = $this->getInvoices($subscription);

        // سجل الأخطاء في الدفع
        $failedPayments = $this->getFailedPayments($subscription);

        return view('admin.subscriptions.show', compact('subscription', 'invoices', 'failedPayments'));
    }

    /**
     * تحديث حالة الاشتراك
     */
    public function updateStatus(Request $request, UserSubscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'status' => 'required|in:active,inactive,trial,suspended,canceled',
            'notes' => 'nullable|string',
        ]);

        $subscription->status = $validated['status'];
        if ($validated['status'] === 'canceled') {
            $subscription->canceled_at = Carbon::now();
        }

        if ($request->has('notes')) {
            $subscription->notes = $validated['notes'];
        }

        $subscription->save();

        // تنظيف الـ Cache
        Cache::forget('subscription_stats');

        return back()->with('success', 'تم تحديث حالة الاشتراك بنجاح.');
    }

    /**
     * تحديث موعد الفاتورة التالية
     */
    public function updateBillingDate(Request $request, UserSubscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'next_billing_date' => 'required|date|after:today',
        ]);

        $subscription->next_billing_date = $validated['next_billing_date'];
        $subscription->save();

        return back()->with('success', 'تم تحديث موعد الفاتورة التالية.');
    }

    /**
     * إضافة رصيد إضافي (Credit Balance)
     */
    public function addCredit(Request $request, UserSubscription $subscription)
    {
        $this->authorize('update', $subscription);

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:255',
        ]);

        $subscription->credit_balance = ($subscription->credit_balance ?? 0) + $validated['amount'];
        $subscription->credit_balance_notes = ($subscription->credit_balance_notes ?? '') .
            "\n[" . Carbon::now() . "] إضافة " . $validated['amount'] . " - " . $validated['reason'];

        $subscription->save();

        return back()->with('success', 'تم إضافة الرصيد بنجاح.');
    }

    /**
     * الحصول على الإحصائيات
     */
    private function getSubscriptionStats()
    {
        return Cache::remember('subscription_stats', 3600, function () {
            $now = Carbon::now();

            return [
                'total_subscriptions' => UserSubscription::count(),
                'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
                'trial_subscriptions' => UserSubscription::where('status', 'trial')->count(),
                'canceled_subscriptions' => UserSubscription::where('status', 'canceled')->count(),
                'suspended_subscriptions' => UserSubscription::where('status', 'suspended')->count(),

                'monthly_recurring' => UserSubscription::where('billing_cycle', 'monthly')
                    ->where('status', 'active')->count(),
                'yearly_recurring' => UserSubscription::where('billing_cycle', 'yearly')
                    ->where('status', 'active')->count(),

                'total_active_users' => User::whereHas('subscriptions', function ($q) {
                    $q->where('status', 'active');
                })->count(),

                'this_month_new' => UserSubscription::whereMonth('created_at', $now->month)
                    ->whereYear('created_at', $now->year)->count(),

                'this_month_canceled' => UserSubscription::whereMonth('canceled_at', $now->month)
                    ->whereYear('canceled_at', $now->year)->count(),

                'mrr' => $this->calculateMRR(),
                'arr' => $this->calculateARR(),
                'avg_subscription_value' => $this->calculateAverageValue(),
                'churn_rate' => $this->calculateChurnRate(),
            ];
        });
    }

    /**
     * حساب MRR (Monthly Recurring Revenue)
     */
    private function calculateMRR()
    {
        return UserSubscription::where('status', 'active')
            ->where('billing_cycle', 'monthly')
            ->sum('current_price') +
            (UserSubscription::where('status', 'active')
                ->where('billing_cycle', 'yearly')
                ->sum('current_price') / 12);
    }

    /**
     * حساب ARR (Annual Recurring Revenue)
     */
    private function calculateARR()
    {
        return $this->calculateMRR() * 12;
    }

    /**
     * حساب متوسط قيمة الاشتراك
     */
    private function calculateAverageValue()
    {
        $total = UserSubscription::where('status', 'active')->sum('current_price');
        $count = UserSubscription::where('status', 'active')->count();
        return $count > 0 ? $total / $count : 0;
    }

    /**
     * حساب معدل الالتفات (Churn Rate)
     */
    private function calculateChurnRate()
    {
        $now = Carbon::now();
        $previousMonth = $now->copy()->subMonth();

        $canceledThisMonth = UserSubscription::whereMonth('canceled_at', $now->month)
            ->whereYear('canceled_at', $now->year)->count();

        $activeAtStartOfMonth = UserSubscription::where('status', 'active')
            ->whereDate('created_at', '<=', $previousMonth->endOfMonth())
            ->count();

        return $activeAtStartOfMonth > 0 ? ($canceledThisMonth / $activeAtStartOfMonth * 100) : 0;
    }

    /**
     * الحصول على الفواتير
     */
    private function getInvoices(UserSubscription $subscription)
    {
        // محاكاة - يمكن الربط بـ Stripe إذا كان متاحاً
        return [
            [
                'id' => 'invoice_' . $subscription->id . '_1',
                'amount' => $subscription->current_price,
                'date' => $subscription->current_period_start,
                'status' => 'paid'
            ],
        ];
    }

    /**
     * الحصول على محاولات الدفع الفاشلة
     */
    private function getFailedPayments(UserSubscription $subscription)
    {
        return [
            // محاكاة - يمكن الربط بـ Stripe
        ];
    }

    /**
     * تصدير الاشتراكات (CSV)
     */
    public function export()
    {
        $user = auth()->user();

        $query = UserSubscription::with(['user', 'subscriptionPlan']);

        if ($user->hasRole('restaurant_owner')) {
            $query->where('user_id', $user->id);
        } elseif (!$user->hasRole('super_admin')) {
            abort(403);
        }

        $subscriptions = $query->get();

        $csv = "المستخدم,البريد,الخطة,الحالة,السعر,دورة الفاتورة,تاريخ البدء\n";

        foreach ($subscriptions as $sub) {
            $csv .= "{$sub->user->name},{$sub->user->email},{$sub->subscriptionPlan->name},{$sub->status},{$sub->current_price},{$sub->billing_cycle},{$sub->started_at}\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="subscriptions.csv"',
        ]);
    }
}
