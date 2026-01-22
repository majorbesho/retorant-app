@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">وكلاء الذكاء الاصطناعي (AI Agents)</h1>
    </div>
    <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">وكلاء AI</li>
        </ol>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">قائمة الوكلاء للمطعم: {{ $restaurant ? $restaurant->name : 'الكل (Super Admin)' }}</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.ai-agents.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> إضافة وكيل جديد
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success mt-2">
                        {{ session('success') }}
                    </div>
                @endif
                
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>النوع</th>
                            <th>الحالة</th>
                            <th>مزود AI</th>
                            <th>الموديل</th>
                            <th>آخر نشاط</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($aiAgents as $agent)
                            <tr>
                                <td>{{ $agent->id }}</td>
                                <td>{{ $agent->name }}</td>
                                <td>
                                    @switch($agent->type)
                                        @case('whatsapp') <span class="badge badge-success">WhatsApp</span> @break
                                        @case('voice') <span class="badge badge-info">صوتي</span> @break
                                        @case('web_chat') <span class="badge badge-primary">شات ويب</span> @break
                                        @case('phone') <span class="badge badge-secondary">هاتف</span> @break
                                        @default <span class="badge badge-light">{{ $agent->type }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($agent->status)
                                        @case('active') <span class="badge badge-success">نشط</span> @break
                                        @case('inactive') <span class="badge badge-danger">غير نشط</span> @break
                                        @case('training') <span class="badge badge-warning">قيد التدريب</span> @break
                                        @case('maintenance') <span class="badge badge-secondary">صيانة</span> @break
                                    @endswitch
                                </td>
                                <td>{{ $agent->ai_provider }}</td>
                                <td>{{ $agent->ai_model }}</td>
                                <td>{{ $agent->last_active_at ? $agent->last_active_at->format('Y-m-d H:i') : '---' }}</td>
                                <td>
                                    <a href="{{ route('admin.ai-agents.edit', $agent->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.ai-agents.destroy', $agent->id) }}" method="POST" class="d-inline">
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
                                <td colspan="8" class="text-center">لا توجد بيانات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $aiAgents->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
