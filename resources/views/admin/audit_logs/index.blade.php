@extends('layouts.adminlte')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">سجل النشاطات (Audit Logs) <span class="badge badge-info">{{ $totalLogs }}</span></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">الرئيسية</a></li>
                    <li class="breadcrumb-item active">سجل النشاطات</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">فلترة السجلات</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.audit-logs.index') }}" method="GET">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>المستخدم (ID)</label>
                                <input type="number" name="user_id" class="form-control" value="{{ request('user_id') }}" placeholder="User ID">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>الحدث (Event)</label>
                                <select name="event" class="form-control">
                                    <option value="">الكل</option>
                                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>إضافة (Created)</option>
                                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>تعديل (Updated)</option>
                                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>حذف (Deleted)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>موديل (Model)</label>
                                <input type="text" name="model" class="form-control" value="{{ request('model') }}" placeholder="e.g. User, Order">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-search"></i> بحث</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>المستخدم</th>
                            <th>الحدث</th>
                            <th>الهدف (Model)</th>
                            <th>التغييرات</th>
                            <th>IP Address</th>
                            <th>التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                @if($log->user)
                                    <span class="badge badge-secondary">{{ $log->user->name }}</span>
                                    <small class="text-muted">#{{ $log->user_id }}</small>
                                @else
                                    <span class="badge badge-light">System/Guest</span>
                                @endif
                            </td>
                            <td>
                                @if($log->event == 'created') <span class="badge badge-success">إضافة</span>
                                @elseif($log->event == 'updated') <span class="badge badge-warning">تعديل</span>
                                @elseif($log->event == 'deleted') <span class="badge badge-danger">حذف</span>
                                @else <span class="badge badge-info">{{ $log->event }}</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ class_basename($log->auditable_type) }}</small>
                                <span class="badge badge-light">#{{ $log->auditable_id }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-xs btn-outline-info" data-toggle="modal" data-target="#modal-log-{{ $log->id }}">
                                    <i class="fas fa-eye"></i> تفاصيل
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="modal-log-{{ $log->id }}">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title">تفاصيل العملية #{{ $log->id }}</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                @if($log->old_values)
                                                <h5>قيم قديمة (Old):</h5>
                                                <pre class="bg-light p-2">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                @endif
                                                
                                                @if($log->new_values)
                                                <h5>قيم جديدة (New):</h5>
                                                <pre class="bg-light p-2">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                @endif

                                                <hr>
                                                <p><strong>URL:</strong> {{ $log->url }}</p>
                                                <p><strong>User Agent:</strong> {{ $log->user_agent }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $log->ip_address }}</td>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">لا توجد سجلات نشاط.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
