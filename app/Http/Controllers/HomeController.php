<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Menu;
use App\Models\Restaurant;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id ?? Restaurant::first()?->id;

        if (!$restaurantId) {
            return view('home', [
                'todaySales' => 0,
                'todayOrdersCount' => 0,
                'totalProducts' => 0,
                'totalMenus' => 0,
                'recentOrders' => collect([])
            ]);
        }

        $today = Carbon::today();

        $todaySales = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        $todayOrdersCount = Order::where('restaurant_id', $restaurantId)
            ->whereDate('created_at', $today)
            ->count();

        $totalProducts = Product::where('restaurant_id', $restaurantId)->count();
        $totalMenus = Menu::where('restaurant_id', $restaurantId)->count();

        $recentOrders = Order::where('restaurant_id', $restaurantId)
            ->latest()
            ->take(10)
            ->get();

        return view('home', compact(
            'todaySales',
            'todayOrdersCount',
            'totalProducts',
            'totalMenus',
            'recentOrders'
        ));
    }
}
