@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --glass-bg: rgba(255, 255, 255, 0.15);
        --glass-border: rgba(255, 255, 255, 0.2);
    }

    body {
        margin: 0;
        padding: 0;
        min-height: 100vh;
        background: fixed var(--primary-gradient);
        font-family: 'Outfit', 'Inter', sans-serif;
    }

    .register-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
    }

    .glass-card {
        background: var(--glass-bg);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 3rem;
        width: 100%;
        max-width: 550px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        color: white;
    }

    .logo-area {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo-icon {
        font-size: 3rem;
        color: white;
        margin-bottom: 0.5rem;
    }

    .register-title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-align: center;
    }

    .register-subtitle {
        text-align: center;
        opacity: 0.8;
        margin-bottom: 2.5rem;
    }

    .role-selection {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .role-item {
        flex: 1;
        cursor: pointer;
        text-align: center;
        padding: 1.2rem 0.5rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .role-item:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-5px);
    }

    .role-item.active {
        background: white;
        color: #764ba2;
        border-color: white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }

    .role-icon {
        font-size: 1.8rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .role-label {
        font-size: 0.85rem;
        font-weight: 600;
        display: block;
    }

    .role-arabic {
        font-size: 0.75rem;
        opacity: 0.7;
        display: block;
    }

    .form-floating > .form-control {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--glass-border);
        color: white;
        border-radius: 12px;
        padding: 1.2rem 1rem;
    }

    .form-floating > .form-control:focus {
        background: rgba(255, 255, 255, 0.2);
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.1);
        border-color: white;
    }

    .form-floating > label {
        color: rgba(255, 255, 255, 0.7);
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: rgba(255, 255, 255, 0.9);
    }

    .custom-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid var(--glass-border);
        color: white;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        transition: all 0.3s;
    }

    .custom-input:focus {
        outline: none;
        background: rgba(255, 255, 255, 0.2);
        border-color: white;
    }

    .custom-input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    select.custom-input option {
        background: #764ba2;
        color: white;
    }

    .invalid-feedback {
        color: #ff9b9b;
        font-size: 0.85rem;
        margin-top: 0.25rem;
        display: block;
    }

    .btn-submit {
        background: white;
        color: #764ba2;
        border: none;
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        font-size: 1.1rem;
        width: 100%;
        margin-top: 2rem;
        transition: all 0.3s;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .btn-submit:hover {
        transform: scale(1.02);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        background: #f8f9fa;
    }

    .login-link {
        text-align: center;
        margin-top: 2rem;
        font-size: 0.9rem;
    }

    .login-link a {
        color: white;
        font-weight: 700;
        text-decoration: underline;
    }

    .role-field {
        transition: all 0.4s ease;
    }
</style>

<div class="register-wrapper">
    <div class="glass-card">
        <div class="logo-area">
            <i class="fas fa-utensils logo-icon"></i>
            <h1 class="register-title">انضم إلينا</h1>
            <p class="register-subtitle">Join the Community. Create your account.</p>
        </div>

        <form action="{{ route('restaurant.join') }}" method="POST">
            @csrf
            
            @if ($errors->any())
                <div class="alert alert-danger" style="background: rgba(220, 38, 38, 0.2); border: 1px solid rgba(220, 38, 38, 0.5); color: #fecaca; padding: 1rem; border-radius: 12px; margin-bottom: 2rem;">
                    <ul style="margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="role-selection">
                <input type="hidden" name="role" id="role-input" value="restaurant_owner">
                
                <div class="role-item active" data-role="restaurant_owner" onclick="setRole('restaurant_owner')">
                    <i class="fas fa-chef role-icon">👨‍🍳</i>
                    <span class="role-label">OWNER</span>
                    <span class="role-label role-arabic">(مدير)</span>
                </div>
                
                <div class="role-item" data-role="restaurant_staff" onclick="setRole('restaurant_staff')">
                    <i class="fas fa-user-tie role-icon">🧑‍💼</i>
                    <span class="role-label">STAFF</span>
                    <span class="role-label role-arabic">(موظف)</span>
                </div>
                
                <div class="role-item" data-role="delivery_driver" onclick="setRole('delivery_driver')">
                    <i class="fas fa-motorcycle role-icon">🛵</i>
                    <span class="role-label">DRIVER</span>
                    <span class="role-label role-arabic">(سائق)</span>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label>الاسم الكامل (Full Name)</label>
                        <input type="text" name="name" class="custom-input @error('name') is-invalid @enderror" placeholder="Enter your full name" required value="{{ old('name') }}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label>البريد الإلكتروني (Email)</label>
                        <input type="email" name="email" class="custom-input @error('email') is-invalid @enderror" placeholder="email@example.com" required value="{{ old('email') }}">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label>رقم الجوال (Phone)</label>
                        <input type="text" name="phone" class="custom-input @error('phone') is-invalid @enderror" placeholder="05XXXXXXXX" required value="{{ old('phone') }}">
                        @error('phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Owner specific -->
                <div class="col-md-12 mb-3 role-field owner-field">
                    <div class="form-group">
                        <label>اسم المطعم (Restaurant Name)</label>
                        <input type="text" name="restaurant_name" class="custom-input owner-input @error('restaurant_name') is-invalid @enderror" placeholder="Enter your restaurant name" value="{{ old('restaurant_name') }}">
                        @error('restaurant_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Staff specific -->
                <div class="col-md-12 mb-3 role-field staff-field" style="display: none;">
                    <div class="form-group">
                        <label>اختر المطعم (Select Restaurant)</label>
                        <select name="restaurant_id" class="custom-input staff-input @error('restaurant_id') is-invalid @enderror">
                            <option value="">اختر مطعم...</option>
                            @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}" {{ old('restaurant_id') == $restaurant->id ? 'selected' : '' }}>{{ $restaurant->name }}</option>
                            @endforeach
                        </select>
                        @error('restaurant_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label>كلمة المرور (Password)</label>
                        <input type="password" name="password" class="custom-input @error('password') is-invalid @enderror" required>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label>تأكيد كلمة المرور (Confirm)</label>
                        <input type="password" name="password_confirmation" class="custom-input" required>
                    </div>
                </div>

                <div class="col-md-12 mb-3 owner-field">
                    <div class="form-group">
                        <label>نوع المطبخ (Cuisine - Optional)</label>
                        <input type="text" name="cuisine_type" class="custom-input" placeholder="e.g. Arabic, Indian...">
                    </div>
                </div>

                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label>رسالة إضافية (Message - Optional)</label>
                        <textarea name="message" class="custom-input" rows="2" placeholder="Anything else we should know?"></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                تسجيل (Sign Up)
            </button>

            <div class="login-link">
                Already have an account? <a href="{{ route('login') }}">Log in</a>
            </div>
        </form>
    </div>
</div>

<script>
    function setRole(role) {
        // Update hidden input
        document.getElementById('role-input').value = role;
        
        // Update UI
        document.querySelectorAll('.role-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-role="${role}"]`).classList.add('active');
        
        // Toggle fields
        const ownerFields = document.querySelectorAll('.owner-field');
        const staffFields = document.querySelectorAll('.staff-field');
        const ownerInputs = document.querySelectorAll('.owner-input');
        const staffInputs = document.querySelectorAll('.staff-input');
        
        if (role === 'restaurant_owner') {
            ownerFields.forEach(f => f.style.display = 'block');
            staffFields.forEach(f => f.style.display = 'none');
            ownerInputs.forEach(i => i.required = true);
            staffInputs.forEach(i => i.required = false);
        } else if (role === 'restaurant_staff') {
            ownerFields.forEach(f => f.style.display = 'none');
            staffFields.forEach(f => f.style.display = 'block');
            ownerInputs.forEach(i => i.required = false);
            staffInputs.forEach(i => i.required = true);
        } else {
            ownerFields.forEach(f => f.style.display = 'none');
            staffFields.forEach(f => f.style.display = 'none');
            ownerInputs.forEach(i => i.required = false);
            staffInputs.forEach(i => i.required = false);
        }
    }

    // Initialize
    window.onload = function() {
        setRole('restaurant_owner');
    }
</script>
@endsection
