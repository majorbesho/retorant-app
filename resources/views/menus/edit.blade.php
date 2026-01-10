@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">تعديل المنيو</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item"><a href="{{ route('menus.index') }}">المنيو</a></li>
            <li class="breadcrumb-item active">تعديل</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">بيانات المنيو</h3>
            </div>
            <form action="{{ route('menus.update', $menu->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label for="name_ar">الاسم (بالعربية)</label>
                        <input type="text" name="name_ar" class="form-control" id="name_ar" value="{{ $menu->name_translations['ar'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="name_en">الاسم (بالنجليزي)</label>
                        <input type="text" name="name_en" class="form-control" id="name_en" value="{{ $menu->name_translations['en'] ?? '' }}" required>
                    </div>
                    <div class="form-group">
                        <label for="description_ar">الووصف (بالعربية)</label>
                        <textarea name="description_ar" class="form-control" id="description_ar">{{ $menu->description_translations['ar'] ?? '' }}</textarea>
                    </div>
                     <div class="form-group">
                        <label for="description_en">الوصف (بالنجليزي)</label>
                        <textarea name="description_en" class="form-control" id="description_en">{{ $menu->description_translations['en'] ?? '' }}</textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">نشط</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    <a href="{{ route('menus.index') }}" class="btn btn-default float-right">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
