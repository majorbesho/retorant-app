@extends('layouts.adminlte')

@section('content-header')
<div class="row mb-2">
    <div class="col-sm-6">
        <h1>تعديل وكيل AI: {{ $aiAgent->name }}</h1>
    </div>
</div>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.ai-agents.update', $aiAgent->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>اسم الوكيل</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $aiAgent->name) }}" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>النوع</label>
                            <select name="type" class="form-control" required>
                                <option value="whatsapp" {{ $aiAgent->type == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                <option value="voice" {{ $aiAgent->type == 'voice' ? 'selected' : '' }}>صوتي (Voice)</option>
                                <option value="web_chat" {{ $aiAgent->type == 'web_chat' ? 'selected' : '' }}>شات ويب</option>
                                <option value="phone" {{ $aiAgent->type == 'phone' ? 'selected' : '' }}>هاتف</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>الحالة</label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ $aiAgent->status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ $aiAgent->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="training" {{ $aiAgent->status == 'training' ? 'selected' : '' }}>تحت التدريب</option>
                                <option value="maintenance" {{ $aiAgent->status == 'maintenance' ? 'selected' : '' }}>صيانة</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>
                <h5>إعدادات الذكاء الاصطناعي</h5>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>المزود (AI Provider)</label>
                            <select name="ai_provider" class="form-control">
                                <option value="openai" {{ $aiAgent->ai_provider == 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                                <option value="anthropic" {{ $aiAgent->ai_provider == 'anthropic' ? 'selected' : '' }}>Anthropic (Claude)</option>
                                <option value="google" {{ $aiAgent->ai_provider == 'google' ? 'selected' : '' }}>Google (Gemini)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>الموديل (AI Model)</label>
                            <select name="ai_model" class="form-control">
                                <option value="gpt-4" {{ $aiAgent->ai_model == 'gpt-4' ? 'selected' : '' }}>GPT-4</option>
                                <option value="gpt-3.5-turbo" {{ $aiAgent->ai_model == 'gpt-3.5-turbo' ? 'selected' : '' }}>GPT-3.5 Turbo</option>
                                <option value="claude-3-opus" {{ $aiAgent->ai_model == 'claude-3-opus' ? 'selected' : '' }}>Claude 3 Opus</option>
                                <option value="claude-3-sonnet" {{ $aiAgent->ai_model == 'claude-3-sonnet' ? 'selected' : '' }}>Claude 3 Sonnet</option>
                                <option value="gemini-pro" {{ $aiAgent->ai_model == 'gemini-pro' ? 'selected' : '' }}>Gemini Pro</option>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-4">
                        <div class="form-group">
                            <label>درجة الحرارة (Temperature: <span id="temp-val">{{ $aiAgent->temperature }}</span>)</label>
                            <input type="range" name="temperature" class="form-control-range" step="0.1" min="0" max="1" value="{{ old('temperature', $aiAgent->temperature) }}" oninput="document.getElementById('temp-val').innerText = this.value">
                        </div>
                    </div>
                </div>

                <hr>
                <h5>الرسائل و الإعدادات (Translations & Config)</h5>
                
                <ul class="nav nav-tabs" id="langTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="ar-tab" data-toggle="pill" href="#ar" role="tab" aria-controls="ar" aria-selected="true">العربية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="en-tab" data-toggle="pill" href="#en" role="tab" aria-controls="en" aria-selected="false">English</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="config-tab" data-toggle="pill" href="#config" role="tab" aria-controls="config" aria-selected="false">إعدادات متقدمة (JSON)</a>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="langTabContent">
                    <div class="tab-pane fade show active" id="ar" role="tabpanel" aria-labelledby="ar-tab">
                        <div class="form-group">
                            <label>رسالة الترحيب (عربي)</label>
                            <textarea name="greeting_ar" class="form-control" rows="3">{{ old('greeting_ar', $aiAgent->greeting_message['ar'] ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>رسالة الفشل/Fallback (عربي)</label>
                            <textarea name="fallback_ar" class="form-control" rows="3">{{ old('fallback_ar', $aiAgent->fallback_message['ar'] ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>ساعات العمل (عربي)</label>
                            <textarea name="working_hours_ar" class="form-control" rows="2">{{ old('working_hours_ar', $aiAgent->working_hours['ar'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="en" role="tabpanel" aria-labelledby="en-tab">
                        <div class="form-group">
                            <label>Greeting Message (English)</label>
                            <textarea name="greeting_en" class="form-control" rows="3">{{ old('greeting_en', $aiAgent->greeting_message['en'] ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Fallback Message (English)</label>
                            <textarea name="fallback_en" class="form-control" rows="3">{{ old('fallback_en', $aiAgent->fallback_message['en'] ?? '') }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Working Hours (English)</label>
                            <textarea name="working_hours_en" class="form-control" rows="2">{{ old('working_hours_en', $aiAgent->working_hours['en'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="config" role="tabpanel" aria-labelledby="config-tab">
                        <div class="row">
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>إعدادات AI (JSON)</label>
                                    <textarea name="ai_config" class="form-control" rows="5">{{ old('ai_config', json_encode($aiAgent->ai_config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                                </div>
                             </div>
                             <div class="col-md-6">
                                <div class="form-group">
                                    <label>إعدادات الصوت (JSON)</label>
                                    <textarea name="voice_settings" class="form-control" rows="5">{{ old('voice_settings', json_encode($aiAgent->voice_settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">تحديث</button>
                <a href="{{ route('admin.ai-agents.index') }}" class="btn btn-secondary mt-3">إلغاء</a>
            </form>
        </div>
    </div>
</div>
@endsection
