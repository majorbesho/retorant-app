@extends('layouts.adminlte')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>تفاصيل طلب التسجيل</h1>
        </div>
        <div class="col-sm-6 text-right">
            <a href="{{ route('admin.restaurant_requests.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        </div>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">بيانات المتقدم</h3>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <th>الاسم بالكامل:</th>
                        <td>{{ $restaurantRequest->name }}</td>
                    </tr>
                    <tr>
                        <th>البريد الإلكتروني:</th>
                        <td>{{ $restaurantRequest->email }}</td>
                    </tr>
                    <tr>
                        <th>رقم الجوال:</th>
                        <td>{{ $restaurantRequest->phone }}</td>
                    </tr>
                    <tr>
                        <th>الدور المطلوب:</th>
                        <td>
                             @php
                                $roles = [
                                    'restaurant_owner' => 'مالك مطعم',
                                    'restaurant_staff' => 'موظف مطعم',
                                    'delivery_driver' => 'سائق توصيل',
                                ];
                            @endphp
                            <span class="badge badge-info">{{ $roles[$restaurantRequest->role] ?? $restaurantRequest->role }}</span>
                        </td>
                    </tr>
                    @if($restaurantRequest->role === 'restaurant_owner')
                        <tr>
                            <th>اسم المطعم المقترح:</th>
                            <td>{{ $restaurantRequest->restaurant_name }}</td>
                        </tr>
                        <tr>
                            <th>نوع المطبخ:</th>
                            <td>{{ $restaurantRequest->cuisine_type }}</td>
                        </tr>
                    @elseif($restaurantRequest->role === 'restaurant_staff')
                        <tr>
                            <th>المطعم المستهدف:</th>
                            <td>{{ $restaurantRequest->restaurant->name ?? 'N/A' }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>الرسالة:</th>
                        <td>{{ $restaurantRequest->message }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        @if($restaurantRequest->status == 'pending')
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">اتخاذ إجراء</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.restaurant_requests.approve', $restaurantRequest) }}" method="POST" class="mb-3">
                    @csrf
                    <div class="form-group">
                        <label>ملاحظات إضافية (اختياري)</label>
                        <textarea name="admin_notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-success btn-lg" onclick="return confirm('هل أنت متأكد من قبول الطلب؟ سيتم تفعيل الحساب فوراً.')">
                        <i class="fas fa-check"></i> قبول الطلب وتفعيل الحساب
                    </button>
                </form>

                <hr>

                <form action="{{ route('admin.restaurant_requests.reject', $restaurantRequest) }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>سبب الرفض (اختياري)</label>
                        <textarea name="admin_notes" class="form-control" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-block btn-danger" onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                        <i class="fas fa-times"></i> رفض الطلب
                    </button>
                </form>
            </div>
        </div>
        @else
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">إجراء متخذ</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-{{ $restaurantRequest->status == 'approved' ? 'success' : 'danger' }}">
                    تم {{ $restaurantRequest->status == 'approved' ? 'قبول' : 'رفض' }} هذا الطلب.
                </div>
                @if($restaurantRequest->admin_notes)
                    <p><strong>ملاحظات المشرف:</strong><br>{{ $restaurantRequest->admin_notes }}</p>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
