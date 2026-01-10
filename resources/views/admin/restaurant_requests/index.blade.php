@extends('layouts.adminlte')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>طلبات تسجيل المطاعم</h1>
        </div>
    </div>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">قائمة الطلبات المستلمة</h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>تاريخ الطلب</th>
                    <th>الاسم</th>
                    <th>الدور المطلوب</th>
                    <th>المطعم</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr>
                    <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $request->name }}<br><small class="text-muted">{{ $request->email }}</small></td>
                    <td>
                        @php
                            $roles = [
                                'restaurant_owner' => 'مالك مطعم',
                                'restaurant_staff' => 'موظف مطعم',
                                'delivery_driver' => 'سائق توصيل',
                            ];
                        @endphp
                        <span class="badge badge-info">{{ $roles[$request->role] ?? $request->role }}</span>
                    </td>
                    <td>
                        @if($request->role === 'restaurant_owner')
                            {{ $request->restaurant_name }} <small class="text-muted">({{ $request->cuisine_type }})</small>
                        @else
                            {{ $request->restaurant_name ?? ($request->restaurant->name ?? 'N/A') }}
                        @endif
                    </td>
                    <td>
                        @if($request->status == 'pending')
                            <span class="badge badge-warning">قيد الانتظار</span>
                        @elseif($request->status == 'approved')
                            <span class="badge badge-success">تم القبول</span>
                        @else
                            <span class="badge badge-danger">مرفوض</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.restaurant_requests.show', $request) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">لا توجد طلبات حالياً</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        {{ $requests->links() }}
    </div>
</div>
@endsection
