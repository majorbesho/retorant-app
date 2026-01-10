@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">خيارات المنتجات (Variations)</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">قائمة التنوعات والخيارات</h3>
            <div class="card-tools">
                <a href="{{ route('variations.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> إضافة تنوع جديد
                </a>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>اسم التنوع</th>
                        <th>إلزامي</th>
                        <th>قواعد الاختيار</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($variations as $variation)
                        <tr>
                            <td>{{ $variation->product->name_translations[app()->getLocale()] ?? $variation->product->name }}</td>
                            <td>{{ $variation->name_translations[app()->getLocale()] ?? $variation->name }}</td>
                            <td>
                                @if($variation->is_required)
                                    <span class="badge badge-warning">نعم</span>
                                @else
                                    <span class="badge badge-secondary">لا</span>
                                @endif
                            </td>
                            <td>{{ $variation->min_selections }} - {{ $variation->max_selections }}</td>
                            <td>
                                <a href="{{ route('variations.edit', $variation->id) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-edit"></i> إدارة
                                </a>
                                <form action="{{ route('variations.destroy', $variation->id) }}" method="POST" class="d-inline">
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
                            <td colspan="5" class="text-center">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
