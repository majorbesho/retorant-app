@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">التصنيفات</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">التصنيفات</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">قائمة التصنيفات</h3>
                <div class="card-tools">
                    <a href="{{ route('categories.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> إضافة تصنيف جديد
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>المنيو</th>
                            <th>التصنيف الأب</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name_translations[app()->getLocale()] ?? $category->name }}</td>
                                <td>{{ $category->menu->name_translations[app()->getLocale()] ?? $category->menu->name ?? '---' }}</td>
                                <td>{{ $category->parent->name_translations[app()->getLocale()] ?? $category->parent->name ?? 'رئيسي' }}</td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline">
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
                                <td colspan="6" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
