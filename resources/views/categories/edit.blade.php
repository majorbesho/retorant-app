@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>تعديل التصنيف</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('categories.update', $category->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الاسم (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar', $category->name_translations['ar'] ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>الوصف (عربي)</label>
                            <textarea name="description_ar" class="form-control">{{ old('description_ar', $category->description_translations['ar'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name (English)</label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en', $category->name_translations['en'] ?? '') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Description (English)</label>
                            <textarea name="description_en" class="form-control">{{ old('description_en', $category->description_translations['en'] ?? '') }}</textarea>
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
                                    <option value="{{ $menu->id }}" {{ $category->menu_id == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name_translations[app()->getLocale()] ?? $menu->name }}
                                    </option>
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
                                    <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name_translations[app()->getLocale()] ?? $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" id="is_active" class="form-check-input" {{ $category->is_active ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">نشط</label>
                </div>
                <button type="submit" class="btn btn-primary mt-3">حفظ التعديلات</button>
                <a href="{{ route('categories.index') }}" class="btn btn-secondary mt-3">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
