@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>إضافة مجموعة إضافات جديدة</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('addon_groups.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>الاسم (عربي)</label>
                            <input type="text" name="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Name (English)</label>
                            <input type="text" name="name_en" class="form-control" value="{{ old('name_en') }}" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>الوصف</label>
                    <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>أقل عدد للاختيار</label>
                            <input type="number" name="min_selections" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>أقصى عدد للاختيار</label>
                            <input type="number" name="max_selections" class="form-control" value="99" min="1">
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_required" id="is_required" class="form-check-input">
                            <label class="form-check-label" for="is_required">إلزامي (يجب اختيار عنصر واحد على الأقل)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" checked>
                            <label class="form-check-label" for="is_active">نشط</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">حفظ واستمرار لإضافة العناصر</button>
                <a href="{{ route('addon_groups.index') }}" class="btn btn-secondary">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
