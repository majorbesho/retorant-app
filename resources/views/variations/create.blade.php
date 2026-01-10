@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>إضافة تنوع للمنتج</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('variations.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>المنتج</label>
                    <select name="product_id" class="form-control select2" required>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $selectedProductId == $product->id ? 'selected' : '' }}>
                                {{ $product->name_translations[app()->getLocale()] ?? $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>اسم التنوع (عربي) - مثلاً: الحجم</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Variation Name (English) - Ex: Size</label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>أقل عدد للاختيار</label>
                            <input type="number" name="min_selections" class="form-control" value="1" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>أقصى عدد للاختيار</label>
                            <input type="number" name="max_selections" class="form-control" value="1" min="1">
                        </div>
                    </div>
                    <div class="col-md-4 mt-4">
                        <div class="form-check">
                            <input type="checkbox" name="is_required" id="is_required" class="form-check-input" checked>
                            <label class="form-check-label" for="is_required">إلزامي</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">حفظ واستمرار لإضافة الخيارات</button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
