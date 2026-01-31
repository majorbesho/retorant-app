@extends('layouts.adminlte')

@section('title', 'إعداد WhatsApp')

@section('content_header')
    <h1>إعداد WhatsApp للمطعم</h1>
@stop

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fab fa-whatsapp"></i>
                        ربط حساب WhatsApp
                    </h3>
                </div>

                <div class="card-body">
                    @if($restaurant->hasWhatsAppConnected())
                        {{-- Connected State --}}
                        <div class="alert alert-success">
                            <h4><i class="icon fas fa-check"></i> متصل!</h4>
                            <p>حساب WhatsApp الخاص بك متصل ويعمل بنجاح.</p>
                            @if($restaurant->whatsapp_number)
                                <p><strong>الرقم:</strong> {{ $restaurant->whatsapp_number }}</p>
                            @endif
                            @if($restaurant->whatsapp_connected_at)
                                <p><strong>تم الاتصال:</strong> {{ $restaurant->whatsapp_connected_at->diffForHumans() }}</p>
                            @endif
                        </div>

                        <div class="text-center mt-4">
                            <form action="{{ route('restaurant.whatsapp.disconnect') }}" method="POST" 
                                  onsubmit="return confirm('هل أنت متأكد من قطع الاتصال؟')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-unlink"></i>
                                    قطع الاتصال
                                </button>
                            </form>
                        </div>

                    @elseif($restaurant->instance_name && $restaurant->whatsapp_status === 'pending')
                        {{-- Pending State - Show QR Code --}}
                        <div class="alert alert-info">
                            <h4><i class="icon fas fa-info"></i> في انتظار المسح</h4>
                            <p>قم بمسح رمز QR التالي باستخدام تطبيق WhatsApp على هاتفك:</p>
                        </div>

                        <div id="qr-code-container" class="text-center my-4">
                            <div id="qr-loading" class="text-center">
                                <i class="fas fa-spinner fa-spin fa-3x text-primary"></i>
                                <p class="mt-3">جاري تحميل رمز QR...</p>
                            </div>
                            <div id="qr-code" style="display: none;">
                                <img id="qr-image" src="" alt="QR Code" class="img-fluid" style="max-width: 400px;">
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <h5>كيفية المسح:</h5>
                            <ol>
                                <li>افتح تطبيق WhatsApp على هاتفك</li>
                                <li>اضغط على القائمة (⋮) أو الإعدادات</li>
                                <li>اختر "الأجهزة المرتبطة"</li>
                                <li>اضغط على "ربط جهاز"</li>
                                <li>امسح رمز QR المعروض أعلاه</li>
                            </ol>
                        </div>

                        <div id="connection-status" class="text-center mt-3">
                            <span class="badge badge-warning badge-lg">
                                <i class="fas fa-clock"></i>
                                في انتظار المسح...
                            </span>
                        </div>

                    @elseif($restaurant->whatsapp_status === 'failed')
                        {{-- Failed State --}}
                        <div class="alert alert-danger">
                            <h4><i class="icon fas fa-exclamation-triangle"></i> فشل الاتصال</h4>
                            <p>حدث خطأ أثناء إنشاء الاتصال. يرجى المحاولة مرة أخرى.</p>
                        </div>

                        <div class="text-center mt-4">
                            <button id="create-instance-btn" class="btn btn-primary btn-lg">
                                <i class="fas fa-redo"></i>
                                إعادة المحاولة
                            </button>
                        </div>

                    @else
                        {{-- Initial State - Not Connected --}}
                        <div class="alert alert-info">
                            <h4><i class="icon fas fa-info"></i> ابدأ الآن</h4>
                            <p>قم بربط حساب WhatsApp الخاص بمطعمك لبدء استقبال الطلبات والرسائل تلقائياً.</p>
                        </div>

                        <div class="text-center my-4">
                            <i class="fab fa-whatsapp fa-5x text-success"></i>
                        </div>

                        <div class="text-center mt-4">
                            <button id="create-instance-btn" class="btn btn-success btn-lg">
                                <i class="fab fa-whatsapp"></i>
                                ربط حساب WhatsApp
                            </button>
                        </div>

                        <div class="alert alert-warning mt-4">
                            <h5>ملاحظات هامة:</h5>
                            <ul>
                                <li>تأكد من أن لديك رقم WhatsApp نشط</li>
                                <li>يجب أن يكون هاتفك متصلاً بالإنترنت</li>
                                <li>سيتم استخدام هذا الحساب لاستقبال طلبات العملاء</li>
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('restaurant.settings') }}" class="btn btn-default">
                        <i class="fas fa-arrow-right"></i>
                        العودة للإعدادات
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>



@stop

@section('js')
<script>
$(document).ready(function() {
    const restaurantStatus = '{{ $restaurant->whatsapp_status ?? "none" }}';
    const hasInstance = {{ $restaurant->instance_name ? 'true' : 'false' }};

    // Configure Toastr
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-left",
        "timeOut": "5000"
    };

    // Create instance button
    $('#create-instance-btn').click(function() {
        const btn = $(this);
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> جاري الاتصال...');
        toastr.info('جاري بدء عملية الاتصال بالسيرفر...');

        $.ajax({
            url: '{{ route("restaurant.whatsapp.create") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    toastr.success('تم إنشاء الجلسة بنجاح، جاري تحضير الباركود...');
                    // Reload page immediately to show QR section
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                } else {
                    toastr.error(response.error || 'تعذر الاتصال بالسيرفر');
                    btn.prop('disabled', false);
                    btn.html('<i class="fas fa-redo"></i> إعادة المحاولة');
                }
            },
            error: function(xhr) {
                let errorMsg = 'تعذر الاتصال، يرجى المحاولة لاحقاً';
                if(xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                toastr.error(errorMsg);
                btn.prop('disabled', false);
                btn.html('<i class="fas fa-redo"></i> إعادة المحاولة');
            }
        });
    });

    // If pending, fetch QR code and poll for status
    if (restaurantStatus === 'pending' && hasInstance) {
        toastr.info('جاري جلب رمز الاستجابة السريع (QR Code)...');
        fetchQRCode();
        startStatusPolling();
    }

    function fetchQRCode() {
        $.ajax({
            url: '{{ route("restaurant.whatsapp.qr") }}',
            method: 'GET',
            success: function(response) {
                if (response.success && response.qrcode) {
                    $('#qr-loading').hide();
                    $('#qr-image').attr('src', response.qrcode);
                    $('#qr-code').show();
                    
                    // Show message only once if not shown before
                    if (!$('#qr-code').data('shown')) {
                        toastr.info('الرجاء مسح الكود باستخدام تطيق واتساب على هاتفك');
                        $('#qr-code').data('shown', true);
                    }
                } else {
                    // Retry after 3 seconds
                    setTimeout(fetchQRCode, 3000);
                }
            },
            error: function() {
                // Retry after 5 seconds
                setTimeout(fetchQRCode, 5000);
            }
        });
    }

    function startStatusPolling() {
        const pollInterval = setInterval(function() {
            $.ajax({
                url: '{{ route("restaurant.whatsapp.status") }}',
                method: 'GET',
                success: function(response) {
                    // If connected
                    if (response.success && response.connected) {
                        clearInterval(pollInterval);
                        toastr.success('تم الاتصال بنجاح! جاري تحديث الصفحة...');
                        setTimeout(function() {
                            window.location.reload();
                        }, 1500);
                    }
                    // If failed
                    else if (response.status === 'failed') {
                        clearInterval(pollInterval);
                        toastr.error('فشل الاتصال، يرجى المحاولة مرة أخرى');
                        setTimeout(function() {
                            window.location.reload();
                        }, 2000);
                    }
                }
            });
        }, 5000); // Poll every 5 seconds

        // Stop polling after 5 minutes
        setTimeout(function() {
            clearInterval(pollInterval);
        }, 300000);
    }
});
</script>
@stop

@section('css')
<style>
    .badge-lg {
        font-size: 1.1rem;
        padding: 0.5rem 1rem;
    }
    
    #qr-code-container {
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card-primary:not(.card-outline) > .card-header {
        background-color: #25D366;
        border-color: #25D366;
    }
    
    .btn-success {
        background-color: #25D366;
        border-color: #25D366;
    }
    
    .btn-success:hover {
        background-color: #128C7E;
        border-color: #128C7E;
    }
</style>
@stop
