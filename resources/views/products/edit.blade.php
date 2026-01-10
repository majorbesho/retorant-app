@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>تعديل المنتج</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-12">
                        @if($product->images && count($product->images) > 0)
                            <label>الصور الحالية</label>
                            <div class="row mb-3">
                                @foreach($product->images as $image)
                                    <div class="col-md-2 mb-2 text-center">
                                        <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail d-block mb-1" style="height: 100px; width: 100%; object-fit: cover;">
                                        <button type="button" class="btn btn-danger btn-xs btn-block" 
                                            onclick="if(confirm('هل أنت متأكد من حذف هذه الصورة؟')) { document.getElementById('delete-img-{{ loop->index }}').submit(); }">
                                            حذف
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <div class="form-group">
                            <label>إضافة صور جديدة</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الاسم (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $product->name_translations['ar'] ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>الوصف (عربي)</label>
                            <textarea name="description_ar" class="form-control">{{ old('description_ar', $product->description_translations['ar'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name (English)</label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $product->name_translations['en'] ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description (English)</label>
                            <textarea name="description_en" class="form-control">{{ old('description_en', $product->description_translations['en'] ?? '') }}</textarea>
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
                                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name_translations[app()->getLocale()] ?? $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>السعر</label>
                            <input type="number" name="price" class="form-control" step="0.01" value="{{ old('price', $product->price) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>الكمية المتوفرة</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>SKU (رمز المنتج)</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $product->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">نشط (يظهر في السيستم)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_available" id="is_available" class="form-check-input" {{ $product->is_available ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_available">متاح للطلب</label>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>مجموعات الإضافات المرتبطة</h5>
                <div class="row">
                    @foreach($addonGroups as $group)
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="addon_groups[]" value="{{ $group->id }}" id="group_{{ $group->id }}" 
                                    class="form-check-input" {{ $product->addonGroups->contains($group->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="group_{{ $group->id }}">
                                    {{ $group->name_translations[app()->getLocale()] ?? $group->name }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                    @if($addonGroups->isEmpty())
                        <div class="col-12 text-muted">لا توجد مجموعات إضافات مضافة حالياً.</div>
                    @endif
                </div>
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>خيارات المنتج (Variations) <small class="text-muted">(مثل: الحجم، الإضافات الإجبارية)</small></h5>
                    <a href="{{ route('variations.create', ['product_id' => $product->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus"></i> إضافة تنوع جديد
                    </a>
                </div>
                <div class="row">
                    @forelse($product->variations as $variation)
                        <div class="col-md-4 mb-2">
                            <div class="info-box bg-light">
                                <div class="info-box-content">
                                    <span class="info-box-text font-weight-bold">
                                        {{ $variation->name_translations[app()->getLocale()] ?? $variation->name }}
                                    </span>
                                    <span class="info-box-number">
                                        <small>{{ $variation->options->count() }} خيارات</small>
                                    </span>
                                    <div class="mt-2">
                                        <a href="{{ route('variations.edit', $variation->id) }}" class="btn btn-xs btn-info">تعديل الخيارات</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-muted">لا توجد تنوعات لهذا المنتج بعد.</div>
                    @endforelse
                </div>
                <hr>

                <button type="submit" class="btn btn-primary mt-3">حفظ التعديلات</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary mt-3">إلغاء</a>
            </form>

            @if($product->images)
                @foreach($product->images as $image)
                    <form id="delete-img-{{ loop->index }}" action="{{ route('products.delete_image', $product->id) }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="image_path" value="{{ $image }}">
                    </form>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection
