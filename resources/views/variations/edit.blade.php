@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>إدارة تنوع: {{ $variation->name_translations[app()->getLocale()] ?? $variation->name }}</h1>
        <small>المنتج: {{ $variation->product->name_translations[app()->getLocale()] ?? $variation->product->name }}</small>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Variation Info -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">إعدادات التنوع</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('variations.update', $variation->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>الاسم (عربي)</label>
                                    <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $variation->name_translations['ar'] ?? '') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name (English)</label>
                                    <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $variation->name_translations['en'] ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>أقل عدد للاختيار</label>
                                    <input type="number" name="min_selections" class="form-control" value="{{ old('min_selections', $variation->min_selections) }}" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>أقصى عدد للاختيار</label>
                                    <input type="number" name="max_selections" class="form-control" value="{{ old('max_selections', $variation->max_selections) }}" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="is_required" id="is_required" class="form-check-input" {{ $variation->is_required ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_required">إلزامي</label>
                        </div>
                        <button type="submit" class="btn btn-primary">تحديث الإعدادات</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Options Management -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">إضافة خيار جديد (مثلاً: كبير)</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('variations.store_option', $variation->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>اسم الخيار (عربي)</label>
                                    <input type="text" name="option_name_ar" class="form-control" placeholder="كبير" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Option Name (EN)</label>
                                    <input type="text" name="option_name_en" class="form-control" placeholder="Large" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>فرق السعر (+ أو -)</label>
                                    <input type="number" name="price_adjustment" class="form-control" step="0.01" value="0.00" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="is_default" id="is_default" class="form-check-input">
                                    <label class="form-check-label" for="is_default">خيار افتراضي</label>
                                </div>
                            </div>
                            <div class="col-md-8 text-right">
                                <button type="submit" class="btn btn-success mt-3">إضافة الخيار</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">الخيارات المتاحة</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>فرق السعر</th>
                                <th>افتراضي</th>
                                <th style="width: 40px">إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($variation->options as $option)
                                <tr>
                                    <td>{{ $option->value_translations[app()->getLocale()] ?? $option->value }}</td>
                                    <td dir="ltr">
                                        @if($option->price_adjustment > 0) +@endif
                                        {{ number_format($option->price_adjustment, 2) }}
                                    </td>
                                    <td>
                                        @if($option->is_default)
                                            <span class="badge badge-success">نعم</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('variation_options.destroy', $option->id) }}" method="POST">
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
                                    <td colspan="4" class="text-center">لا توجد خيارات مضافة بعد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <a href="{{ route('products.edit', $variation->product_id) }}" class="btn btn-secondary btn-block">
                <i class="fas fa-arrow-right"></i> العودة للمنتج
            </a>
        </div>
    </div>
</div>
@endsection
