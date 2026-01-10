@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>إضافة تصنيف جديد</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>المنيو</label>
                            <select name="menu_id" class="form-control" required>
                                <option value="">اختر المنيو</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name_translations[app()->getLocale()] ?? $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>التصنيف الأب (اختياري)</label>
                            <select name="parent_id" class="form-control">
                                <option value="">تصنيف رئيسي</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name_translations[app()->getLocale()] ?? $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                    <label class="form-check-label" for="is_active">نشط</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">حفظ</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-3">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
