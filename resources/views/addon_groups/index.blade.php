@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">مجموعات الإضافات</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">مجموعات الإضافات</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">قائمة مجموعات الإضافات</h3>
                <div class="card-tools">
                    <a href="{{ route('addon_groups.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> إضافة مجموعة جديدة
                    </a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>عدد العناصر</th>
                            <th>قواعد الاختيار</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($groups as $group)
                            <tr>
                                <td>{{ $group->id }}</td>
                                <td>{{ $group->name_translations[app()->getLocale()] ?? $group->name }}</td>
                                <td>{{ $group->addons_count }}</td>
                                <td>
                                    {{ $group->is_required ? 'إجباري' : 'اختياري' }} 
                                    (من {{ $group->min_selections }} إلى {{ $group->max_selections }})
                                </td>
                                <td>
                                    @if($group->is_active)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('addon_groups.edit', $group->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i> إدارة العناصر
                                    </a>
                                    <form action="{{ route('addon_groups.destroy', $group->id) }}" method="POST" class="d-inline">
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
