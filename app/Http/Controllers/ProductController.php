<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\AddonGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $products = Product::where('restaurant_id', $restaurantId)->with('category')->get();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $categories = Category::where('restaurant_id', $restaurantId)->get();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
        ]);

        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;

        if (!$restaurantId) {
            return redirect()->back()->withErrors(['error' => 'لا يوجد مطعم متاح.']);
        }

        $product = new Product();
        $product->restaurant_id = $restaurantId;
        $product->category_id = $request->category_id;
        $product->sku = $request->sku ?? 'SKU-' . time();
        $product->name = $request->name_en;
        $product->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $product->description = $request->description_en;
        $product->description_translations = ['ar' => $request->description_ar, 'en' => $request->description_en];
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity ?? 0;
        $product->is_active = $request->has('is_active');
        $product->is_available = $request->has('is_available');
        $product->save();

        return redirect()->route('products.index')->with('success', 'تم إضافة المنتج بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $restaurantId = auth()->user()->restaurant_id ?? $product->restaurant_id;
        $categories = Category::where('restaurant_id', $restaurantId)->get();
        $addonGroups = AddonGroup::where('restaurant_id', $restaurantId)->get();
        return view('products.edit', compact('product', 'categories', 'addonGroups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
        ]);

        $product->category_id = $request->category_id;
        $product->sku = $request->sku ?? $product->sku;
        $product->name = $request->name_en;
        $product->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $product->description = $request->description_en;
        $product->description_translations = ['ar' => $request->description_ar, 'en' => $request->description_en];
        $product->price = $request->price;
        $product->stock_quantity = $request->stock_quantity ?? 0;
        $product->is_active = $request->has('is_active');
        $product->is_available = $request->has('is_available');

        // Handling Images
        if ($request->hasFile('images')) {
            $currentImages = $product->images ?? [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $currentImages[] = $path;
            }
            $product->images = $currentImages;
        }

        $product->save();

        if ($request->has('addon_groups')) {
            $product->addonGroups()->sync($request->addon_groups);
        } else {
            $product->addonGroups()->sync([]);
        }

        return redirect()->route('products.index')->with('success', 'تم تحديث المنتج بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'تم حذف المنتج بنجاح');
    }

    public function deleteImage(Product $product, Request $request)
    {
        $imagePath = $request->image_path;
        $images = $product->images;

        if (($key = array_search($imagePath, $images)) !== false) {
            unset($images[$key]);
            Storage::disk('public')->delete($imagePath);
            $product->images = array_values($images);
            $product->save();
            return redirect()->back()->with('success', 'تم حذف الصورة بنجاح.');
        }

        return redirect()->back()->with('error', 'الصورة غير موجودة.');
    }
}
