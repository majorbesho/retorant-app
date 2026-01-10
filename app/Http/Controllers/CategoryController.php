<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $categories = Category::where('restaurant_id', $restaurantId)->with('menu')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $menus = Menu::where('restaurant_id', $restaurantId)->get();
        $parentCategories = Category::where('restaurant_id', $restaurantId)->get();
        return view('categories.create', compact('menus', 'parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;

        if (!$restaurantId) {
            return redirect()->back()->withErrors(['error' => 'لا يوجد مطعم متاح.']);
        }

        $category = new Category();
        $category->restaurant_id = $restaurantId;
        $category->menu_id = $request->menu_id;
        $category->parent_id = $request->parent_id;
        $category->name = $request->name_en;
        $category->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $category->description = $request->description_en;
        $category->description_translations = ['ar' => $request->description_ar, 'en' => $request->description_en];
        $category->is_active = $request->has('is_active');
        $category->save();

        return redirect()->route('categories.index')->with('success', 'تم إنشاء التصنيف بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $restaurantId = auth()->user()->restaurant_id ?? $category->restaurant_id;
        $menus = Menu::where('restaurant_id', $restaurantId)->get();
        $parentCategories = Category::where('restaurant_id', $restaurantId)->where('id', '!=', $category->id)->get();
        return view('categories.edit', compact('category', 'menus', 'parentCategories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $category->menu_id = $request->menu_id;
        $category->parent_id = $request->parent_id;
        $category->name = $request->name_en;
        $category->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $category->description = $request->description_en;
        $category->description_translations = ['ar' => $request->description_ar, 'en' => $request->description_en];
        $category->is_active = $request->has('is_active');
        $category->save();

        return redirect()->route('categories.index')->with('success', 'تم تحديث التصنيف بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'تم حذف التصنيف بنجاح');
    }
}
