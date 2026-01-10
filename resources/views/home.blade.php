@extends('layouts.adminlte')

@section('content-header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">لوحة التحكم</h1>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ number_format($todaySales, 2) }} <small style="font-size: 15px;">ر.س</small></h3>
                    <p>مبيعات اليوم</p>
                </div>
                <div class="icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <a href="#" class="small-box-footer">المزيد من التفاصيل <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $todayOrdersCount }}</h3>
                    <p>طلبات اليوم</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <a href="#" class="small-box-footer">عرض الطلبات <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $totalProducts }}</h3>
                    <p>إجمالي المنتجات</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
                <a href="{{ route('products.index') }}" class="small-box-footer">إدارة المنتجات <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $totalMenus }}</h3>
                    <p>القوائم (Menus)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <a href="{{ route('menus.index') }}" class="small-box-footer">إدارة القوائم <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">آخر الطلبات المستلمة</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>العميل</th>
                                    <th>الحالة</th>
                                    <th>المبلغ الإجمالي</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr>
                                        <td><a href="#">{{ $order->order_number }}</a></td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($order->status) {
                                                    'delivered' => 'badge-success',
                                                    'pending' => 'badge-warning',
                                                    'preparing' => 'badge-info',
                                                    'cancelled' => 'badge-danger',
                                                    default => 'badge-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ __($order->status) }}</span>
                                        </td>
                                        <td>{{ number_format($order->total_amount, 2) }} ر.س</td>
                                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">لا توجد طلبات حديثة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="javascript:void(0)" class="btn btn-sm btn-secondary float-right">عرض جميع الطلبات</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

