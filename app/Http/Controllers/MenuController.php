<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Cache Key strategy: menus_page_{page_number}
        $page = request()->get('page', 1);
        $cacheKey = 'menus_list_page_' . $page;

        $menus = Cache::remember($cacheKey, 600, function () {
            // Paginate 15 items per page for speed
            return Menu::latest()->paginate(15);
        });

        return view('menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('menus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $restaurantId = auth()->user()->restaurant_id;

        if (!$restaurantId) {
            $firstRestaurant = \App\Models\Restaurant::first();
            if ($firstRestaurant) {
                $restaurantId = $firstRestaurant->id;
            } else {
                return redirect()->back()->withErrors(['restaurant_id' => 'لا يوجد مطعم متاح لربط المنيو به.']);
            }
        }

        $menu = new Menu();
        $menu->restaurant_id = $restaurantId;
        $menu->name = $request->name_en; // Default name
        $menu->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $menu->description = $request->description_en;
        $menu->description_translations = ['ar' => $request->description_ar, 'en' => $request->description_en];
        $menu->is_active = $request->has('is_active');
        $menu->save();

        // Clear Cache
        Cache::flush(); // Simple invalidation or use tags if supported

        return redirect()->route('menus.index')->with('success', 'تم إنشاء المنيو بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Menu $menu)
    {
        return view('menus.edit', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $menu->name = $request->name_en;
        $menu->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $menu->description = $request->description_en;
        $menu->description_translations = ['ar' => $request->description_ar, 'en' => $request->description_en];
        $menu->is_active = $request->has('is_active');
        $menu->save();

        // Clear Cache
        Cache::flush();

        return redirect()->route('menus.index')->with('success', 'تم تحديث المنيو بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();
        // Clear Cache
        Cache::flush();

        return redirect()->route('menus.index')->with('success', 'تم حذف المنيو بنجاح');
    }
}
