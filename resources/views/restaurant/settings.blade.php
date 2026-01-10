@extends('layouts.adminlte')

@section('content-header')
<div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>إعدادات المطعم</h1>
        </div>
    </div>
</div>
@endsection

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        --accent-color: #3b82f6;
    }

    .glass-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,.125);
        border-radius: 15px;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        overflow: hidden;
    }

    .nav-tabs-custom {
        border-bottom: 2px solid #f1f5f9;
        background: #fff;
    }

    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        padding: 1rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
        border-bottom: 3px solid transparent;
    }

    .nav-tabs-custom .nav-link.active {
        color: var(--accent-color);
        background: transparent;
        border-bottom-color: var(--accent-color);
    }

    .tab-content-custom {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
        display: block;
        text-align: right;
    }

    .custom-input {
        width: 100%;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.3s;
        text-align: right;
    }

    .custom-input:focus {
        outline: none;
        background: #fff;
        border-color: var(--accent-color);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .btn-save {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 0.75rem 2.5rem;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.3s;
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .logo-preview {
        width: 100px;
        height: 100px;
        border-radius: 15px;
        object-fit: cover;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }

    .gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .gallery-item {
        aspect-ratio: 1;
        border-radius: 10px;
        object-fit: cover;
        width: 100%;
    }

    .ai-switch-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1.25rem;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.25rem;
        text-align: right;
        border-right: 4px solid var(--accent-color);
        padding-right: 0.75rem;
    }
</style>

<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 10px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-check"></i> تم بنجاح!</h5>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" style="border-radius: 10px;">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-ban"></i> خطأ!</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <form action="{{ route('restaurant.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card">
                @csrf
                @method('PUT')

                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs nav-tabs-custom" id="settingsTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="general-tab" data-toggle="pill" href="#general" role="tab" aria-controls="general" aria-selected="true">المعلومات الأساسية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="media-tab" data-toggle="pill" href="#media" role="tab" aria-controls="media" aria-selected="false">الصور واللوجو</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="contact-tab" data-toggle="pill" href="#contact" role="tab" aria-controls="contact" aria-selected="false">الاتصال والتواصل</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="ai-tab" data-toggle="pill" href="#ai" role="tab" aria-controls="ai" aria-selected="false">الذكاء الاصطناعي</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="offers-tab" data-toggle="pill" href="#offers" role="tab" aria-controls="offers" aria-selected="false">العروض</a>
                        </li>
                    </ul>
                </div>

                <div class="card-body tab-content-custom">
                    <div class="tab-content" id="settingsTabsContent">
                        <!-- General Info -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">اسم المطعم</label>
                                    <input type="text" name="name" class="custom-input" value="{{ old('name', $restaurant->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رابط الموقع الإلكتروني</label>
                                    <input type="url" name="website" class="custom-input" value="{{ old('website', $restaurant->website) }}" placeholder="https://example.com">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">نوع المطبخ (Cuisine)</label>
                                    <input type="text" name="cuisine_type" class="custom-input" value="{{ old('cuisine_type', $restaurant->cuisine_type) }}" placeholder="مثلاً: إيطالي، عربي، وجبات سريعة">
                                </div>
                            </div>
                        </div>

                        <!-- Media -->
                        <div class="tab-pane fade" id="media" role="tabpanel">
                            <div class="row">
                                <div class="col-md-12 mb-5">
                                    <h3 class="section-title">شعار المطعم (Logo)</h3>
                                    <div class="d-flex align-items-center flex-row-reverse">
                                        <img src="{{ $restaurant->logo ? asset('storage/'.$restaurant->logo) : 'https://via.placeholder.com/100' }}" class="logo-preview" id="logoPreview">
                                        <div class="mr-4 text-right">
                                            <input type="file" name="logo" class="form-control-file mb-2" onchange="previewImage(this, 'logoPreview')">
                                            <small class="text-muted">نوصي بصورة مربعة (JPG, PNG) بحد أقصى 2 ميجابايت</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h3 class="section-title">معرض الصور (Gallery)</h3>
                                    <input type="file" name="gallery[]" class="form-control-file mb-3" multiple>
                                    <div class="gallery-grid">
                                        @foreach($restaurant->gallery ?? [] as $img)
                                            <img src="{{ asset('storage/'.$img) }}" class="gallery-item">
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact -->
                        <div class="tab-pane fade" id="contact" role="tabpanel">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">البريد الإلكتروني للعمل</label>
                                    <input type="email" name="email" class="custom-input" value="{{ old('email', $restaurant->email) }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">رقم الهاتف الأساسي</label>
                                    <input type="text" name="phone" class="custom-input" value="{{ old('phone', $restaurant->phone) }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">رقم الواتساب (للطلبات والمحادثات)</label>
                                    <input type="text" name="whatsapp_number" class="custom-input" value="{{ old('whatsapp_number', $restaurant->whatsapp_number) }}" placeholder="966XXXXXXXXX">
                                    <small class="text-muted d-block mt-1 text-end">تأكد من كتابة الرقم مع مفتاح الدولة وبدون أصفار إضافية</small>
                                </div>
                            </div>
                        </div>

                        <!-- AI & Subscription -->
                        <div class="tab-pane fade" id="ai" role="tabpanel">
                            <h3 class="section-title">خدمات الذكاء الاصطناعي (AI Agents)</h3>
                            
                            <div class="ai-switch-card">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="ai_whatsapp" name="ai_whatsapp" value="1" {{ old('ai_whatsapp', data_get($restaurant->settings, 'ai_whatsapp')) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ai_whatsapp"></label>
                                </div>
                                <div class="text-right">
                                    <span class="d-block font-weight-bold">مساعد واتساب الذكي</span>
                                    <small class="text-muted">تفعيل الرد التلقائي والحجز عبر الواتساب باستخدام الذكاء الاصطناعي</small>
                                </div>
                            </div>

                            <div class="ai-switch-card">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="ai_calls" name="ai_calls" value="1" {{ old('ai_calls', data_get($restaurant->settings, 'ai_calls')) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ai_calls"></label>
                                </div>
                                <div class="text-right">
                                    <span class="d-block font-weight-bold">مساعد الاتصالات الصوتي</span>
                                    <small class="text-muted">الرد على المكالمات الهاتفية، توضيح المنيو واستقبال الحجوزات صوتياً</small>
                                </div>
                            </div>

                            <div class="ai-switch-card">
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="ai_automation" name="ai_automation" value="1" {{ old('ai_automation', data_get($restaurant->settings, 'ai_automation')) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="ai_automation"></label>
                                </div>
                                <div class="text-right">
                                    <span class="d-block font-weight-bold">أتمتة سير العمل (Workflow Automation)</span>
                                    <small class="text-muted">توصيل الطلبات تلقائياً بالسيستم وإرسال إشعارات للفريق</small>
                                </div>
                            </div>

                            <div class="mt-4 p-3 bg-light border rounded" style="border-right: 4px solid #007bff !important;">
                                <div class="d-flex align-items-center justify-content-between flex-row-reverse">
                                    <div class="text-right">
                                        <h5 class="mb-1">خطة الاشتراك الحالية</h5>
                                        <p class="text-primary mb-0 font-weight-bold">{{ $restaurant->subscription_details['plan_name'] ?? 'لم يتم الاشتراك' }}</p>
                                    </div>
                                    <a href="#" class="btn btn-primary btn-sm rounded-pill px-3">ترقية الاشتراك</a>
                                </div>
                            </div>
                        </div>

                        <!-- Offers -->
                        <div class="tab-pane fade" id="offers" role="tabpanel">
                            <h3 class="section-title">إدارة العروض والترويج</h3>
                            <div class="form-group">
                                <label class="form-label">نص العروض الحالية (تظهر للعملاء في الواتساب والموقع)</label>
                                <textarea name="offers" class="custom-input" rows="5" placeholder="اكتب تفاصيل العروض المتاحة حالياً هنا...">{{ data_get($restaurant->settings, 'offers_text') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light text-center">
                    <button type="submit" class="btn-save shadow">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
