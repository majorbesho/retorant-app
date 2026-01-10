<?php

namespace App\Http\Controllers;

use App\Models\Variation;
use App\Models\VariationOption;
use App\Models\Product;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $variations = Variation::whereHas('product', function ($q) use ($restaurantId) {
            $q->where('restaurant_id', $restaurantId);
        })->with('product')->get();

        return view('variations.index', compact('variations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $products = Product::where('restaurant_id', $restaurantId)->get();
        $selectedProductId = $request->product_id;

        return view('variations.create', compact('products', 'selectedProductId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $variation = new Variation();
        $variation->product_id = $request->product_id;
        $variation->name = $request->name_en;
        $variation->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $variation->is_required = $request->has('is_required');
        $variation->min_selections = $request->min_selections ?? 1;
        $variation->max_selections = $request->max_selections ?? 1;
        $variation->save();

        return redirect()->route('variations.edit', $variation->id)->with('success', 'تم إنشاء التنوع، يمكنك الآن إضافة الخيارات.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Variation $variation)
    {
        $variation->load('options');
        return view('variations.edit', compact('variation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Variation $variation)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
        ]);

        $variation->name = $request->name_en;
        $variation->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $variation->is_required = $request->has('is_required');
        $variation->min_selections = $request->min_selections ?? 1;
        $variation->max_selections = $request->max_selections ?? 1;
        $variation->save();

        return redirect()->back()->with('success', 'تم تحديث البيانات بنجاح.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Variation $variation)
    {
        $productId = $variation->product_id;
        $variation->delete();
        return redirect()->route('products.edit', $productId)->with('success', 'تم حذف التنوع بنجاح.');
    }

    /**
     * Store Option
     */
    public function storeOption(Request $request, Variation $variation)
    {
        $request->validate([
            'option_name_ar' => 'required|string|max:255',
            'option_name_en' => 'required|string|max:255',
            'price_adjustment' => 'required|numeric',
        ]);

        $option = new VariationOption();
        $option->variation_id = $variation->id;
        $option->value = $request->option_name_en;
        $option->value_translations = ['ar' => $request->option_name_ar, 'en' => $request->option_name_en];
        $option->price_adjustment = $request->price_adjustment;
        $option->stock_quantity = $request->stock_quantity ?? -1;
        $option->is_active = true;
        $option->save();

        if ($request->has('is_default')) {
            VariationOption::where('variation_id', $variation->id)->where('id', '!=', $option->id)->update(['is_default' => false]);
            $option->is_default = true;
            $option->save();
        }

        return redirect()->back()->with('success', 'تم إضافة الخيار بنجاح.');
    }

    /**
     * Destroy Option
     */
    public function destroyOption(VariationOption $option)
    {
        $option->delete();
        return redirect()->back()->with('success', 'تم حذف الخيار بنجاح.');
    }
}
