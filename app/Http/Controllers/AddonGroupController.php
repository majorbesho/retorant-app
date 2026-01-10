<?php

namespace App\Http\Controllers;

use App\Models\AddonGroup;
use App\Models\Addon;
use Illuminate\Http\Request;

class AddonGroupController extends Controller
{
    public function index()
    {
        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;
        $groups = AddonGroup::where('restaurant_id', $restaurantId)->withCount('addons')->get();
        return view('addon_groups.index', compact('groups'));
    }

    public function create()
    {
        return view('addon_groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'min_selections' => 'required|integer|min:0',
            'max_selections' => 'required|integer|min:1',
        ]);

        $restaurantId = auth()->user()->restaurant_id ?? \App\Models\Restaurant::first()?->id;

        if (!$restaurantId) {
            return redirect()->back()->withErrors(['error' => 'لا يوجد مطعم متاح.']);
        }

        $group = new AddonGroup();
        $group->restaurant_id = $restaurantId;
        $group->name = $request->name_en;
        $group->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $group->description = $request->description;
        $group->is_required = $request->has('is_required');
        $group->min_selections = $request->min_selections;
        $group->max_selections = $request->max_selections;
        $group->is_active = $request->has('is_active');
        $group->save();

        return redirect()->route('addon_groups.edit', $group->id)->with('success', 'تم إنشاء مجموعة الإضافات بنجاح. يمكنك الآن إضافة العناصر.');
    }

    public function edit(AddonGroup $addonGroup)
    {
        $addonGroup->load('addons');
        return view('addon_groups.edit', compact('addonGroup'));
    }

    public function update(Request $request, AddonGroup $addonGroup)
    {
        $request->validate([
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'min_selections' => 'required|integer|min:0',
            'max_selections' => 'required|integer|min:1',
        ]);

        $addonGroup->name = $request->name_en;
        $addonGroup->name_translations = ['ar' => $request->name_ar, 'en' => $request->name_en];
        $addonGroup->description = $request->description;
        $addonGroup->is_required = $request->has('is_required');
        $addonGroup->min_selections = $request->min_selections;
        $addonGroup->max_selections = $request->max_selections;
        $addonGroup->is_active = $request->has('is_active');
        $addonGroup->save();

        return redirect()->route('addon_groups.index')->with('success', 'تم تحديث مجموعة الإضافات بنجاح');
    }

    public function destroy(AddonGroup $addonGroup)
    {
        $addonGroup->delete();
        return redirect()->route('addon_groups.index')->with('success', 'تم حذف مجموعة الإضافات بنجاح');
    }

    // Addon management methods
    public function storeAddon(Request $request, AddonGroup $addonGroup)
    {
        $request->validate([
            'addon_name_ar' => 'required|string|max:255',
            'addon_name_en' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $addon = new Addon();
        $addon->addon_group_id = $addonGroup->id;
        $addon->name = $request->addon_name_en;
        $addon->name_translations = ['ar' => $request->addon_name_ar, 'en' => $request->addon_name_en];
        $addon->price = $request->price;
        $addon->stock_quantity = $request->stock_quantity ?? -1;
        $addon->is_active = true;
        $addon->save();

        return redirect()->back()->with('success', 'تم إضافة العنصر بنجاح');
    }

    public function destroyAddon(Addon $addon)
    {
        $addon->delete();
        return redirect()->back()->with('success', 'تم حذف العنصر بنجاح');
    }
}
