@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>إضافة منتج جديد</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>صور المنتج (يمكنك اختيار أكثر من صورة)</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الاسم (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                        </div>
                        <div class="form-group">
                            <label>الوصف (عربي)</label>
                            <textarea name="description_ar" class="form-control">{{ old('description_ar') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name (English)</label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description (English)</label>
                            <textarea name="description_en" class="form-control">{{ old('description_en') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>التصنيف</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">اختر التصنيف</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name_translations[app()->getLocale()] ?? $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>السعر</label>
                            <input type="number" name="price" class="form-control" step="0.01" value="{{ old('price') }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>الكمية المتوفرة</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 0) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>SKU (رمز المنتج)</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku') }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                            <label class="form-check-label" for="is_active">نشط (يظهر في السيستم)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_available" id="is_available" class="form-check-input" checked>
                            <label class="form-check-label" for="is_available">متاح للطلب</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">حفظ</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
