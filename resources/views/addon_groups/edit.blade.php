@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>إدارة مجموعة الإضافات: {{ $addonGroup->name_translations[app()->getLocale()] ?? $addonGroup->name }}</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Group Settings -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إعدادات المجموعة</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('addon_groups.update', $addonGroup->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الاسم (عربي)</label>
                                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $addonGroup->name_translations['ar'] ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name (English)</label>
                                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $addonGroup->name_translations['en'] ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>الوصف</label>
                            <textarea name="description" class="form-control">{{ old('description', $addonGroup->description) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>أقل عدد للاختيار</label>
                                    <input type="number" name="min_selections" class="form-control" value="{{ old('min_selections', $addonGroup->min_selections) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>أقصى عدد للاختيار</label>
                                    <input type="number" name="max_selections" class="form-control" value="{{ old('max_selections', $addonGroup->max_selections) }}" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="form-check mb-2">
                            <input type="checkbox" name="is_required" id="is_required" class="form-check-input" {{ $addonGroup->is_required ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required">إلزامي</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $addonGroup->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                        <button type="submit" class="btn btn-primary">تحديث الإعدادات</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Addons Management -->
        <div class="col-md-7">
            <!-- Add New Addon Form -->
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">إضافة عنصر جديد لهذه المجموعة</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('addon_groups.store_addon', $addonGroup->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>الاسم (عربي)</label>
                                    <input type="text" name="addon_name_ar" class="form-control" placeholder="مثلاً: جبن إضافي" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Name (English)</label>
                                    <input type="text" name="addon_name_en" class="form-control" placeholder="Ex: Extra Cheese" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>السعر</label>
                                    <input type="number" name="price" class="form-control" step="0.01" value="0.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>المخزون (-1 لا نهائي)</label>
                                    <input type="number" name="stock_quantity" class="form-control" value="-1">
                                </div>
                            </div>
                            <div class="col-md-8 text-right">
                                <button type="submit" class="btn btn-success mt-4">
                                    <i class="fas fa-plus"></i> إضافة العنصر
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List of Addons -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">عناصر المجموعة الحالية</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>السعر</th>
                                <th>المخزون</th>
                                <th style="width: 40px">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($addonGroup->addons as $addon)
                                <tr>
                                    <td>{{ $addon->name_translations[app()->getLocale()] ?? $addon->name }}</td>
                                    <td>{{ number_format($addon->price, 2) }}</td>
                                    <td>{{ $addon->stock_quantity == -1 ? '∞' : $addon->stock_quantity }}</td>
                                    <td>
                                        <form action="{{ route('addons.destroy', $addon->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد عناصر مضافة بعد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
