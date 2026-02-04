<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('app.name') }} - {{ __('app.tagline') }}</title>
    <meta name="description" content="{{ __('app.meta_description') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    @if(app()->getLocale() == 'en')
        <link rel="stylesheet" href="{{ asset('css/landing-ltr.css') }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header -->
    <header class="main-header" id="main-header">
        <div class="container">
            <div class="logo">
                <a href="#">
                    <span class="logo-icon">🤖</span>
                    <span class="logo-text">Retorant <span class="highlight">AI</span></span>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#features">{{ __('nav.features') }}</a></li>
                    <li><a href="#how-it-works">{{ __('nav.how_it_works') }}</a></li>
                    <li><a href="#testimonials">{{ __('nav.testimonials') }}</a></li>
                    <li><a href="#pricing">{{ __('nav.pricing') }}</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <!-- Language Switcher -->
                @if(app()->getLocale() == 'ar')
                    <a href="{{ route('locale.switch', 'en') }}" class="lang-switch">English</a>
                @else
                    <a href="{{ route('locale.switch', 'ar') }}" class="lang-switch"style="color: #fafafa;" >العربية</a>
                @endif
                
                <a href="{{ route('login') }}" class="cta-button secondary">{{ __('nav.login') }}</a>
            </div>
            
            <button class="mobile-menu-toggle" id="mobile-menu-toggle">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section">
        <canvas id="hero-canvas"></canvas>
        <div class="hero-content-wrapper container">
            <div class="hero-badge">{{ __('hero.badge') }}</div>
            <h1 class="hero-headline">
                <span class="gradient-text">{{ __('hero.headline_start') }}</span>
                <span>{{ __('hero.headline_end') }}</span>
            </h1>
            <p class="hero-subheadline">
                {{ __('hero.subheadline') }}
            </p>
            <div class="hero-ctas">
                <a href="{{ route('register') }}" class="cta-button primary">
                    <span>{{ __('hero.cta_primary') }}</span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
                <a href="#demo" class="cta-button outline">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="M8 7L13 10L8 13V7Z" fill="currentColor"/>
                    </svg>
                    <span>{{ __('hero.cta_secondary') }}</span>
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">{{ __('hero.stat_conversations') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">{{ __('hero.stat_satisfaction') }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">{{ __('hero.stat_availability') }}</div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <span>{{ __('hero.scroll_more') }}</span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 5V19M12 19L5 12M12 19L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </section>

    <!-- Problem-Solution Section -->
    <section class="problem-solution-section" id="how-it-works">
        <div class="container">
            <h2 class="section-title">{{ __('problems.title') }}</h2>
            <p class="section-description">
                {{ __('problems.description') }}
            </p>

            <div class="content-grid">
                <div class="problem-card card">
                    <div class="card-icon problem-icon">⚠️</div>
                    <h3 class="card-title">{{ __('problems.card_title') }}</h3>
                    <ul class="problem-list">
                        <li>
                            <span class="list-icon">💸</span>
                            <div>
                                <h4>{{ __('problems.items.cost.title') }}</h4>
                                <p>{{ __('problems.items.cost.desc') }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">⏰</span>
                            <div>
                                <h4>{{ __('problems.items.hours.title') }}</h4>
                                <p>{{ __('problems.items.hours.desc') }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">😓</span>
                            <div>
                                <h4>{{ __('problems.items.errors.title') }}</h4>
                                <p>{{ __('problems.items.errors.desc') }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">📱</span>
                            <div>
                                <h4>{{ __('problems.items.channels.title') }}</h4>
                                <p>{{ __('problems.items.channels.desc') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="solution-card card">
                    <div class="card-icon solution-icon">✨</div>
                    <h3 class="card-title">{{ __('solutions.card_title') }}</h3>
                    <ul class="solution-list">
                        <li>
                            <span class="list-icon">🤖</span>
                            <div>
                                <h4>{{ __('solutions.items.automation.title') }}</h4>
                                <p>{{ __('solutions.items.automation.desc') }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">🌙</span>
                            <div>
                                <h4>{{ __('solutions.items.availability.title') }}</h4>
                                <p>{{ __('solutions.items.availability.desc') }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">✅</span>
                            <div>
                                <h4>{{ __('solutions.items.accuracy.title') }}</h4>
                                <p>{{ __('solutions.items.accuracy.desc') }}</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">🔗</span>
                            <div>
                                <h4>{{ __('solutions.items.integration.title') }}</h4>
                                <p>{{ __('solutions.items.integration.desc') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-in-action-section" id="features">
        <div class="container">
            <h2 class="section-title">{{ __('features.title') }}</h2>
            <p class="section-description">
                {{ __('features.description') }}
            </p>

            <div class="feature-showcase-grid">
                <!-- Chat -->
                <div class="feature-item card">
                    <div class="feature-icon">💬</div>
                    <h3 class="feature-title">{{ __('features.items.chat.title') }}</h3>
                    <p class="feature-description">
                        {{ __('features.items.chat.desc') }}
                    </p>
                    <ul class="feature-points">
                        @foreach(__('features.items.chat.points') as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Orders -->
                <div class="feature-item card">
                    <div class="feature-icon">🍔</div>
                    <h3 class="feature-title">{{ __('features.items.orders.title') }}</h3>
                    <p class="feature-description">
                        {{ __('features.items.orders.desc') }}
                    </p>
                    <ul class="feature-points">
                        @foreach(__('features.items.orders.points') as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Analytics -->
                <div class="feature-item card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">{{ __('features.items.analytics.title') }}</h3>
                    <p class="feature-description">
                        {{ __('features.items.analytics.desc') }}
                    </p>
                    <ul class="feature-points">
                        @foreach(__('features.items.analytics.points') as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Integrations -->
                <div class="feature-item card">
                    <div class="feature-icon">🔗</div>
                    <h3 class="feature-title">{{ __('features.items.integrations.title') }}</h3>
                    <p class="feature-description">
                        {{ __('features.items.integrations.desc') }}
                    </p>
                    <ul class="feature-points">
                        @foreach(__('features.items.integrations.points') as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Customization -->
                <div class="feature-item card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">{{ __('features.items.customization.title') }}</h3>
                    <p class="feature-description">
                        {{ __('features.items.customization.desc') }}
                    </p>
                    <ul class="feature-points">
                        @foreach(__('features.items.customization.points') as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>

                <!-- Security -->
                <div class="feature-item card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">{{ __('features.items.security.title') }}</h3>
                    <p class="feature-description">
                        {{ __('features.items.security.desc') }}
                    </p>
                    <ul class="feature-points">
                        @foreach(__('features.items.security.points') as $point)
                            <li>✓ {{ $point }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works-section">
        <div class="container">
            <h2 class="section-title">{{ __('how_it_works.title') }}</h2>
            <p class="section-description">
                {{ __('how_it_works.description') }}
            </p>

            <div class="steps-container">
                <div class="step-item">
                    <div class="step-number">01</div>
                    <div class="step-icon">🚀</div>
                    <h3 class="step-title">{{ __('how_it_works.steps.1.title') }}</h3>
                    <p class="step-description">
                        {{ __('how_it_works.steps.1.desc') }}
                    </p>
                </div>

                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-number">02</div>
                    <div class="step-icon">⚙️</div>
                    <h3 class="step-title">{{ __('how_it_works.steps.2.title') }}</h3>
                    <p class="step-description">
                        {{ __('how_it_works.steps.2.desc') }}
                    </p>
                </div>

                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-number">03</div>
                    <div class="step-icon">✅</div>
                    <h3 class="step-title">{{ __('how_it_works.steps.3.title') }}</h3>
                    <p class="step-description">
                        {{ __('how_it_works.steps.3.desc') }}
                    </p>
                </div>
            </div>

            <div class="cta-bottom">
                <a href="{{ route('register') }}" class="cta-button primary">{{ __('how_it_works.cta') }}</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <h2 class="section-title">{{ __('testimonials.title') }}</h2>
            <p class="section-description">
                {{ __('testimonials.description') }}
            </p>

            <div class="testimonials-carousel">
                @foreach(__('testimonials.items') as $testimonial)
                <div class="testimonial-item card">
                    <div class="quote-icon">"</div>
                    <p class="client-quote">
                        {{ $testimonial['quote'] }}
                    </p>
                    <div class="client-info">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($testimonial['name']) }}&background=007bff&color=fff" alt="{{ $testimonial['name'] }}" class="client-avatar">
                        <div>
                            <div class="client-name">{{ $testimonial['name'] }}</div>
                            <div class="client-position">{{ $testimonial['position'] }}</div>
                        </div>
                    </div>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <h2 class="section-title">{{ __('pricing.title') }}</h2>
            <p class="section-description">{{ __('pricing.description') }}</p>

            <div class="pricing-toggle">
                <button class="toggle-button active" data-billing-cycle="monthly">{{ __('pricing.monthly') }}</button>
                <button class="toggle-button" data-billing-cycle="yearly">
                    {{ __('pricing.yearly') }}
                    <span class="discount-badge">{{ __('pricing.save_badge') }}</span>
                </button>
            </div>

            <div class="pricing-cards-grid">
                <!-- Starter -->
                <div class="pricing-card card" data-plan-id="starter">
                    <div class="plan-header">
                        <h3 class="plan-name">{{ __('pricing.plans.starter.name') }}</h3>
                        <p class="plan-subtitle">{{ __('pricing.plans.starter.subtitle') }}</p>
                    </div>
                    <div class="plan-price">
                        <span class="currency">{{ __('pricing.currency') }}</span>
                        <span class="price-value" data-monthly="29" data-yearly="290">29</span>
                        <span class="billing-cycle">{{ __('pricing.per_month') }}</span>
                    </div>
                    <ul class="plan-features">
                        @foreach(__('pricing.plans.starter.features') as $feature)
                        <li><span class="check-icon">✓</span> {{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="cta-button outline">{{ __('pricing.plans.starter.cta') }}</a>
                </div>

                <!-- Professional -->
                <div class="pricing-card card recommended" data-plan-id="professional">
                    <div class="plan-badge">{{ __('pricing.plans.professional.badge') }}</div>
                    <div class="plan-header">
                        <h3 class="plan-name">{{ __('pricing.plans.professional.name') }}</h3>
                        <p class="plan-subtitle">{{ __('pricing.plans.professional.subtitle') }}</p>
                    </div>
                    <div class="plan-price">
                        <span class="currency">{{ __('pricing.currency') }}</span>
                        <span class="price-value" data-monthly="79" data-yearly="790">79</span>
                        <span class="billing-cycle">{{ __('pricing.per_month') }}</span>
                    </div>
                    <ul class="plan-features">
                        @foreach(__('pricing.plans.professional.features') as $feature)
                        <li><span class="check-icon">✓</span> {{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ route('register') }}" class="cta-button primary">{{ __('pricing.plans.professional.cta') }}</a>
                </div>

                <!-- Enterprise -->
                <div class="pricing-card card" data-plan-id="enterprise">
                    <div class="plan-header">
                        <h3 class="plan-name">{{ __('pricing.plans.enterprise.name') }}</h3>
                        <p class="plan-subtitle">{{ __('pricing.plans.enterprise.subtitle') }}</p>
                    </div>
                    <div class="plan-price">
                        <span class="price-value custom">{{ __('pricing.plans.enterprise.custom_price') }}</span>
                    </div>
                    <ul class="plan-features">
                        @foreach(__('pricing.plans.enterprise.features') as $feature)
                        <li><span class="check-icon">✓</span> {{ $feature }}</li>
                        @endforeach
                    </ul>
                    <a href="#contact-us" class="cta-button outline">{{ __('pricing.plans.enterprise.cta') }}</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">
            <h2 class="section-title">{{ __('faq.title') }}</h2>
            <p class="section-description">
                {{ __('faq.description') }}
            </p>

            <div class="faq-accordion">
                @foreach(__('faq.items') as $faq)
                <div class="faq-item">
                    <button class="faq-question">
                        <span>{{ $faq['q'] }}</span>
                        <span class="arrow-icon">▼</span>
                    </button>
                    <div class="faq-answer">
                        <p>{{ $faq['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>{{ __('cta_section.title') }}</h2>
                <p>{{ __('cta_section.subtitle') }}</p>
                <a href="{{ route('register') }}" class="cta-button primary large">
                    <span>{{ __('cta_section.button') }}</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
                <p class="cta-note">{{ __('cta_section.note') }}</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-about">
                    <div class="footer-logo">
                        <span class="logo-icon">🤖</span>
                        <span>Retorant <span class="highlight">AI</span></span>
                    </div>
                    <p>{{ __('footer.about') }}</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
                        <a href="#" aria-label="Twitter"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg></a>
                        <a href="#" aria-label="Instagram"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                        <a href="#" aria-label="LinkedIn"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>{{ __('footer.product') }}</h4>
                    <ul>
                        <li><a href="#features">{{ __('footer.links.features') }}</a></li>
                        <li><a href="#pricing">{{ __('footer.links.pricing') }}</a></li>
                        <li><a href="#how-it-works">{{ __('footer.links.how_it_works') }}</a></li>
                        <li><a href="#faq">{{ __('footer.links.faq') }}</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>{{ __('footer.company') }}</h4>
                    <ul>
                        <li><a href="#about">{{ __('footer.links.about') }}</a></li>
                        <li><a href="#blog">{{ __('footer.links.blog') }}</a></li>
                        <li><a href="#careers">{{ __('footer.links.careers') }}</a></li>
                        <li><a href="#contact">{{ __('footer.links.contact') }}</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>{{ __('footer.support') }}</h4>
                    <ul>
                        <li><a href="#help">{{ __('footer.links.help') }}</a></li>
                        <li><a href="#docs">{{ __('footer.links.docs') }}</a></li>
                        <li><a href="#privacy">{{ __('footer.links.privacy') }}</a></li>
                        <li><a href="#terms">{{ __('footer.links.terms') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>{{ __('footer.copyright') }}</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
