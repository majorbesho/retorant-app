@extends('layouts.app')

@section('content')
<div class="container" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
    <div class="card fade-in-up" style="max-width: 600px; width: 100%; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); border: none; overflow: hidden;">
        <div style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 3rem; text-align: center; color: white;">
            <div style="font-size: 5rem; margin-bottom: 1rem;">⏳</div>
            <h2 style="font-weight: 700; margin-bottom: 0.5rem;">حسابك قيد المراجع الآن</h2>
            <p style="opacity: 0.9;">شكراً لك على التسجيل في منصتنا!</p>
        </div>
        <div class="card-body" style="padding: 3rem; text-align: center;">
            <p style="font-size: 1.1rem; color: #4b5563; line-height: 1.8; margin-bottom: 2rem;">
                لقد استلمنا طلبك بنجاح. فريقنا يقوم حالياً بمراجعة بياناتك لضمان جودة المنصة. سيتم تفعيل حسابك بمجرد الموافقة من قبل الإدارة.
            </p>
            
            <div style="background: #f3f4f6; padding: 1.5rem; border-radius: 12px; text-align: right; margin-bottom: 2rem;">
                <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 0.5rem; font-size: 1rem;">ماذا يحدث الآن؟</h4>
                <ul style="list-style: none; padding: 0; margin: 0; color: #6b7280; font-size: 0.95rem;">
                    <li style="margin-bottom: 0.5rem;">✅ مراجعة البيانات من قبل الإدارة المختصة.</li>
                    <li style="margin-bottom: 0.5rem;">✅ التواصل معك في حال وجود نقص في البيانات.</li>
                    <li>✅ إرسال إشعار تفعيل الحساب فور الموافقة.</li>
                </ul>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-secondary" style="border-radius: 10px; padding: 0.8rem 2rem;">
                    تسجيل الخروج
                </button>
            </form>
            
            <p style="margin-top: 2rem; font-size: 0.9rem; color: #9ca3af;">
                هل تعتقد أن هناك خطأ؟ <a href="mailto:support@example.com" style="color: #3b82f6; text-decoration: none;">تواصل معنا</a>
            </p>
        </div>
    </div>
</div>
@endsection
