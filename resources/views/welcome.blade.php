<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="{{ __('app.description') }}">
    
    <title>{{ __('app.name') }} - {{ __('app.tagline') }}</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    
    <!-- SEO Meta Tags -->
    <meta name="robots" content="index, follow">
    <meta name="author" content="{{ __('app.name') }}">
    <meta property="og:title" content="{{ __('app.name') }}">
    <meta property="og:description" content="{{ __('app.description') }}">
    <meta property="og:type" content="website">
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="container navbar-container">
            @if(session('success'))
                <div class="alert alert-success" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 1001; background: #10b981; color: white; padding: 1rem 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                    {{ session('success') }}
                </div>
            @endif
            <a href="/" class="navbar-logo">
                {{ __('app.name') }}
            </a>
            
            <ul class="navbar-menu">
                <li><a href="#features" class="navbar-link">{{ __('nav.features') }}</a></li>
                <li><a href="#pricing" class="navbar-link">{{ __('nav.pricing') }}</a></li>
                <li><a href="#testimonials" class="navbar-link">{{ __('nav.testimonials') }}</a></li>
                
                <!-- Language Switcher -->
                <li>
                    <a href="{{ route('locale.switch', app()->getLocale() == 'ar' ? 'en' : 'ar') }}" 
                       class="language-switcher">
                        <svg class="language-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                        </svg>
                        {{ __('nav.language') }}
                    </a>
                </li>
                
                @if (Route::has('login'))
                    @auth
                        <li><a href="{{ url('/home') }}" class="btn btn-primary">Dashboard</a></li>
                    @else
                        <li><a href="{{ route('login') }}" class="navbar-link">{{ __('nav.login') }}</a></li>
                        @if (Route::has('register'))
                            <li><a href="{{ route('register') }}" class="btn btn-primary">{{ __('nav.get_started') }}</a></li>
                        @endif
                    @endauth
                @endif
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-background">
            <!-- Animated background elements can be added here -->
        </div>
        
        <div class="container hero-content fade-in-up">
            <div class="hero-badge">
                🚀 {{ __('hero.trusted_by') }}
            </div>
            
            <h1 class="hero-title">
                {{ __('hero.title') }}
            </h1>
            
            <p class="hero-subtitle">
                {{ __('hero.subtitle') }}
            </p>
            
            <p class="hero-description">
                {{ __('hero.description') }}
            </p>
            
            <div class="hero-cta">
                <a href="{{ route('register') }}" class="btn btn-primary btn-large">
                    {{ __('hero.cta_primary') }}
                </a>
                <a href="#" class="btn btn-secondary btn-large">
                    {{ __('hero.cta_secondary') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section features" id="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">{{ __('features.title') }}</h2>
                <p class="section-subtitle">{{ __('features.subtitle') }}</p>
            </div>
            
            <div class="features-grid">
                <!-- AI Chat Feature -->
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">💬</div>
                    <h3 class="feature-title">{{ __('features.items.ai_chat.title') }}</h3>
                    <p class="feature-description">{{ __('features.items.ai_chat.description') }}</p>
                </div>
                
                <!-- Order Management Feature -->
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">📦</div>
                    <h3 class="feature-title">{{ __('features.items.order_management.title') }}</h3>
                    <p class="feature-description">{{ __('features.items.order_management.description') }}</p>
                </div>
                
                <!-- Analytics Feature -->
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">{{ __('features.items.analytics.title') }}</h3>
                    <p class="feature-description">{{ __('features.items.analytics.description') }}</p>
                </div>
                
                <!-- Multilingual Feature -->
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">🌍</div>
                    <h3 class="feature-title">{{ __('features.items.multilingual.title') }}</h3>
                    <p class="feature-description">{{ __('features.items.multilingual.description') }}</p>
                </div>
                
                <!-- Integrations Feature -->
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">🔗</div>
                    <h3 class="feature-title">{{ __('features.items.integrations.title') }}</h3>
                    <p class="feature-description">{{ __('features.items.integrations.description') }}</p>
                </div>
                
                <!-- Customization Feature -->
                <div class="feature-card fade-in-up">
                    <div class="feature-icon">⚙️</div>
                    <h3 class="feature-title">{{ __('features.items.customization.title') }}</h3>
                    <p class="feature-description">{{ __('features.items.customization.description') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="section section-alt" id="pricing">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">{{ __('pricing.title') }}</h2>
                <p class="section-subtitle">{{ __('pricing.subtitle') }}</p>
            </div>
            
            <div class="pricing-grid">
                <!-- Starter Plan -->
                <div class="pricing-card">
                    <h3 class="pricing-name">{{ __('pricing.plans.starter.name') }}</h3>
                    <div class="pricing-price">{{ __('pricing.plans.starter.price') }}<span class="pricing-period">{{ __('pricing.plans.starter.period') }}</span></div>
                    <p class="pricing-description">{{ __('pricing.plans.starter.description') }}</p>
                    
                    <ul class="pricing-features">
                        @foreach(__('pricing.plans.starter.features') as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    
                    <a href="{{ route('register') }}" class="btn btn-secondary" style="width: 100%;">
                        {{ __('pricing.plans.starter.cta') }}
                    </a>
                </div>
                
                <!-- Professional Plan (Popular) -->
                <div class="pricing-card popular">
                    <h3 class="pricing-name">{{ __('pricing.plans.professional.name') }}</h3>
                    <div class="pricing-price">{{ __('pricing.plans.professional.price') }}<span class="pricing-period">{{ __('pricing.plans.professional.period') }}</span></div>
                    <p class="pricing-description">{{ __('pricing.plans.professional.description') }}</p>
                    
                    <ul class="pricing-features">
                        @foreach(__('pricing.plans.professional.features') as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    
                    <a href="{{ route('register') }}" class="btn btn-primary" style="width: 100%;">
                        {{ __('pricing.plans.professional.cta') }}
                    </a>
                </div>
                
                <!-- Enterprise Plan -->
                <div class="pricing-card">
                    <h3 class="pricing-name">{{ __('pricing.plans.enterprise.name') }}</h3>
                    <div class="pricing-price">{{ __('pricing.plans.enterprise.price') }}<span class="pricing-period">{{ __('pricing.plans.enterprise.period') }}</span></div>
                    <p class="pricing-description">{{ __('pricing.plans.enterprise.description') }}</p>
                    
                    <ul class="pricing-features">
                        @foreach(__('pricing.plans.enterprise.features') as $feature)
                            <li>{{ $feature }}</li>
                        @endforeach
                    </ul>
                    
                    <a href="#contact" class="btn btn-secondary" style="width: 100%;">
                        {{ __('pricing.plans.enterprise.cta') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="section" id="testimonials">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">{{ __('testimonials.title') }}</h2>
                <p class="section-subtitle">{{ __('testimonials.subtitle') }}</p>
            </div>
            
            <div class="testimonials-grid">
                @foreach(__('testimonials.items') as $testimonial)
                    <div class="testimonial-card">
                        <div class="testimonial-rating">
                            @for($i = 0; $i < $testimonial['rating']; $i++)
                                ⭐
                            @endfor
                        </div>
                        
                        <p class="testimonial-text">"{{ $testimonial['text'] }}"</p>
                        
                        <div class="testimonial-author">
                            <div class="testimonial-avatar"></div>
                            <div>
                                <div class="testimonial-name">{{ $testimonial['name'] }}</div>
                                <div class="testimonial-role">{{ $testimonial['role'] }}, {{ $testimonial['location'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="section-title">{{ __('cta.title') }}</h2>
            <p class="section-subtitle mb-lg">{{ __('cta.subtitle') }}</p>
            
            <a href="{{ route('register') }}" class="btn btn-large" style="background: white; color: var(--primary-color);">
                {{ __('cta.button') }}
            </a>
            
            <p class="cta-note">{{ __('cta.no_credit_card') }}</p>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h4>{{ __('app.name') }}</h4>
                    <p>{{ __('footer.tagline') }}</p>
                </div>
                
                <div class="footer-column">
                    <h4>{{ __('footer.product') }}</h4>
                    <ul class="footer-links">
                        <li><a href="#features">{{ __('footer.links.features') }}</a></li>
                        <li><a href="#pricing">{{ __('footer.links.pricing') }}</a></li>
                        <li><a href="#">{{ __('footer.links.integrations') }}</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>{{ __('footer.company') }}</h4>
                    <ul class="footer-links">
                        <li><a href="#">{{ __('footer.links.about') }}</a></li>
                        <li><a href="#">{{ __('footer.links.careers') }}</a></li>
                        <li><a href="#">{{ __('footer.links.blog') }}</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h4>{{ __('footer.support') }}</h4>
                    <ul class="footer-links">
                        <li><a href="#">{{ __('footer.links.help_center') }}</a></li>
                        <li><a href="#">{{ __('footer.links.contact') }}</a></li>
                        <li><a href="#">{{ __('footer.links.api_docs') }}</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>{{ __('footer.copyright') }}</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
    <script>
        document.getElementById('role-select').addEventListener('change', function() {
            const role = this.value;
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
        });

        // Initialize required state
        window.addEventListener('DOMContentLoaded', () => {
             document.getElementById('role-select').dispatchEvent(new Event('change'));
        });
    </script>
</body>
</html>
