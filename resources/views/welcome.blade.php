<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retorant AI - وكيل ذكاء اصطناعي لمطعمك</title>
    <meta name="description" content="حوّل خدمة عملاء مطعمك مع وكيل AI ذكي يعمل 24/7 عبر واتساب والقنوات الرقمية">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
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
                    <li><a href="#features">الميزات</a></li>
                    <li><a href="#how-it-works">كيف يعمل</a></li>
                    <li><a href="#testimonials">آراء العملاء</a></li>
                    <li><a href="#pricing">الأسعار</a></li>
                </ul>
            </nav>
            <a href="{{ route('login') }}" class="cta-button secondary">تسجيل الدخول</a>
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
            <div class="hero-badge">🚀 الجيل الجديد من خدمة العملاء</div>
            <h1 class="hero-headline">
                <span class="gradient-text">وكيل AI ذكي</span>
                <span>لمطعمك يعمل 24/7</span>
            </h1>
            <p class="hero-subheadline">
                أتمتة المحادثات، الطلبات، والحجوزات عبر واتساب والقنوات الرقمية. وفّر 70% من تكاليف خدمة العملاء.
            </p>
            <div class="hero-ctas">
                <a href="{{ route('register') }}" class="cta-button primary">
                    <span>ابدأ تجربتك المجانية</span>
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M7.5 15L12.5 10L7.5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
                <a href="#demo" class="cta-button outline">
                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="8" stroke="currentColor" stroke-width="2"/>
                        <path d="M8 7L13 10L8 13V7Z" fill="currentColor"/>
                    </svg>
                    <span>شاهد العرض التوضيحي</span>
                </a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">10K+</div>
                    <div class="stat-label">محادثة شهرياً</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">95%</div>
                    <div class="stat-label">رضا العملاء</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">24/7</div>
                    <div class="stat-label">متاح دائماً</div>
                </div>
            </div>
        </div>
        <div class="scroll-indicator">
            <span>اكتشف المزيد</span>
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M12 5V19M12 19L5 12M12 19L19 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
    </section>

    <!-- Problem-Solution Section -->
    <section class="problem-solution-section" id="how-it-works">
        <div class="container">
            <h2 class="section-title">التحديات التي نحلها</h2>
            <p class="section-description">
                نحوّل مشاكل خدمة العملاء إلى فرص نمو لمطعمك
            </p>

            <div class="content-grid">
                <div class="problem-card card">
                    <div class="card-icon problem-icon">⚠️</div>
                    <h3 class="card-title">التحديات الحالية</h3>
                    <ul class="problem-list">
                        <li>
                            <span class="list-icon">💸</span>
                            <div>
                                <h4>تكاليف تشغيل مرتفعة</h4>
                                <p>رواتب الموظفين وتكاليف التدريب المستمر</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">⏰</span>
                            <div>
                                <h4>ساعات عمل محدودة</h4>
                                <p>فقدان الطلبات خارج أوقات الدوام</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">😓</span>
                            <div>
                                <h4>أخطاء بشرية</h4>
                                <p>أخطاء في الطلبات تؤدي لعدم رضا العملاء</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">📱</span>
                            <div>
                                <h4>قنوات متعددة معقدة</h4>
                                <p>صعوبة إدارة واتساب، الهاتف، والموقع</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="solution-card card">
                    <div class="card-icon solution-icon">✨</div>
                    <h3 class="card-title">الحل الذكي</h3>
                    <ul class="solution-list">
                        <li>
                            <span class="list-icon">🤖</span>
                            <div>
                                <h4>أتمتة كاملة</h4>
                                <p>وكيل AI يعمل بدون توقف بتكلفة ثابتة</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">🌙</span>
                            <div>
                                <h4>خدمة 24/7</h4>
                                <p>استقبال الطلبات والاستفسارات في أي وقت</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">✅</span>
                            <div>
                                <h4>دقة 99%</h4>
                                <p>معالجة دقيقة للطلبات بدون أخطاء</p>
                            </div>
                        </li>
                        <li>
                            <span class="list-icon">🔗</span>
                            <div>
                                <h4>تكامل موحّد</h4>
                                <p>إدارة جميع القنوات من لوحة تحكم واحدة</p>
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
            <h2 class="section-title">ميزات قوية لنجاح مطعمك</h2>
            <p class="section-description">
                كل ما تحتاجه لتحويل خدمة العملاء إلى تجربة استثنائية
            </p>

            <div class="feature-showcase-grid">
                <div class="feature-item card">
                    <div class="feature-icon">💬</div>
                    <h3 class="feature-title">محادثات ذكية</h3>
                    <p class="feature-description">
                        فهم طبيعي للغة العربية والإنجليزية مع ردود سياقية دقيقة
                    </p>
                    <ul class="feature-points">
                        <li>✓ معالجة اللغة الطبيعية</li>
                        <li>✓ ذاكرة المحادثة</li>
                        <li>✓ تحليل المشاعر</li>
                    </ul>
                </div>

                <div class="feature-item card">
                    <div class="feature-icon">🍔</div>
                    <h3 class="feature-title">إدارة الطلبات</h3>
                    <p class="feature-description">
                        استقبال ومعالجة الطلبات تلقائياً مع تأكيد فوري
                    </p>
                    <ul class="feature-points">
                        <li>✓ قائمة طعام ديناميكية</li>
                        <li>✓ حساب التكلفة الآلي</li>
                        <li>✓ تتبع الطلبات</li>
                    </ul>
                </div>

                <div class="feature-item card">
                    <div class="feature-icon">📊</div>
                    <h3 class="feature-title">تحليلات متقدمة</h3>
                    <p class="feature-description">
                        رؤى عميقة حول سلوك العملاء وأداء المطعم
                    </p>
                    <ul class="feature-points">
                        <li>✓ لوحة تحكم شاملة</li>
                        <li>✓ تقارير تفصيلية</li>
                        <li>✓ توقعات ذكية</li>
                    </ul>
                </div>

                <div class="feature-item card">
                    <div class="feature-icon">🔗</div>
                    <h3 class="feature-title">تكامل سلس</h3>
                    <p class="feature-description">
                        اتصال مباشر مع واتساب، أنظمة POS، وأدوات التوصيل
                    </p>
                    <ul class="feature-points">
                        <li>✓ WhatsApp Business API</li>
                        <li>✓ تكامل POS</li>
                        <li>✓ منصات التوصيل</li>
                    </ul>
                </div>

                <div class="feature-item card">
                    <div class="feature-icon">🎯</div>
                    <h3 class="feature-title">تخصيص كامل</h3>
                    <p class="feature-description">
                        شخصية الوكيل، نبرة الصوت، وسيناريوهات مخصصة لعلامتك
                    </p>
                    <ul class="feature-points">
                        <li>✓ شخصية قابلة للتعديل</li>
                        <li>✓ سيناريوهات مخصصة</li>
                        <li>✓ علامة تجارية متسقة</li>
                    </ul>
                </div>

                <div class="feature-item card">
                    <div class="feature-icon">🔒</div>
                    <h3 class="feature-title">أمان وخصوصية</h3>
                    <p class="feature-description">
                        حماية بيانات العملاء مع التزام كامل بمعايير الأمان
                    </p>
                    <ul class="feature-points">
                        <li>✓ تشفير end-to-end</li>
                        <li>✓ GDPR متوافق</li>
                        <li>✓ نسخ احتياطي آمن</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works-section">
        <div class="container">
            <h2 class="section-title">ابدأ في 3 خطوات بسيطة</h2>
            <p class="section-description">
                من التسجيل إلى الإطلاق في أقل من 10 دقائق
            </p>

            <div class="steps-container">
                <div class="step-item">
                    <div class="step-number">01</div>
                    <div class="step-icon">🚀</div>
                    <h3 class="step-title">سجّل وأضف مطعمك</h3>
                    <p class="step-description">
                        أنشئ حسابك، أدخل معلومات المطعم، وارفع قائمة الطعام
                    </p>
                </div>

                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-number">02</div>
                    <div class="step-icon">⚙️</div>
                    <h3 class="step-title">خصّص وكيلك الذكي</h3>
                    <p class="step-description">
                        اختر الشخصية، النبرة، واللغة التي تناسب علامتك التجارية
                    </p>
                </div>

                <div class="step-connector"></div>

                <div class="step-item">
                    <div class="step-number">03</div>
                    <div class="step-icon">✅</div>
                    <h3 class="step-title">انطلق واستقبل العملاء</h3>
                    <p class="step-description">
                        اربط واتساب وابدأ باستقبال الطلبات والمحادثات فوراً
                    </p>
                </div>
            </div>

            <div class="cta-bottom">
                <a href="{{ route('register') }}" class="cta-button primary">ابدأ الآن مجاناً</a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials-section" id="testimonials">
        <div class="container">
            <h2 class="section-title">قصص نجاح عملائنا</h2>
            <p class="section-description">
                مطاعم حقيقية حققت نتائج استثنائية
            </p>

            <div class="testimonials-carousel">
                <div class="testimonial-item card">
                    <div class="quote-icon">"</div>
                    <p class="client-quote">
                        زادت طلباتنا بنسبة 40% بعد تفعيل Retorant AI. الوكيل يعمل حتى بعد إغلاق المطعم!
                    </p>
                    <div class="client-info">
                        <img src="https://ui-avatars.com/api/?name=Ahmed+Alshami&background=007bff&color=fff" alt="أحمد الشامي" class="client-avatar">
                        <div>
                            <div class="client-name">أحمد الشامي</div>
                            <div class="client-position">مالك مطعم أطايب الشام</div>
                        </div>
                    </div>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>

                <div class="testimonial-item card">
                    <div class="quote-icon">"</div>
                    <p class="client-quote">
                        وفرنا تكلفة موظفين بقيمة 15,000 ريال شهرياً. الاستثمار الأفضل لمطعمنا!
                    </p>
                    <div class="client-info">
                        <img src="https://ui-avatars.com/api/?name=Fatima+Zahra&background=28a745&color=fff" alt="فاطمة الزهراء" class="client-avatar">
                        <div>
                            <div class="client-name">فاطمة الزهراء</div>
                            <div class="client-position">مديرة مقهى ركن القهوة</div>
                        </div>
                    </div>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>

                <div class="testimonial-item card">
                    <div class="quote-icon">"</div>
                    <p class="client-quote">
                        التحليلات ساعدتنا نفهم عملائنا أكثر. نعرف الآن أكثر الأطباق طلباً والأوقات الأكثر ازدحاماً.
                    </p>
                    <div class="client-info">
                        <img src="https://ui-avatars.com/api/?name=Mohammed+Abdullah&background=ffc107&color=333" alt="محمد عبدالله" class="client-avatar">
                        <div>
                            <div class="client-name">محمد عبدالله</div>
                            <div class="client-position">مدير سلسلة النجمة الذهبية</div>
                        </div>
                    </div>
                    <div class="rating">⭐⭐⭐⭐⭐</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing -->
    <section class="pricing-section" id="pricing">
        <div class="container">
            <h2 class="section-title">خطط مرنة لكل حجم مطعم</h2>
            <p class="section-description">ابدأ مجاناً، وادفع فقط عند النمو</p>

            <div class="pricing-toggle">
                <button class="toggle-button active" data-billing-cycle="monthly">شهرياً</button>
                <button class="toggle-button" data-billing-cycle="yearly">
                    سنوياً
                    <span class="discount-badge">وفّر 20%</span>
                </button>
            </div>

            <div class="pricing-cards-grid">
                <div class="pricing-card card" data-plan-id="starter">
                    <div class="plan-header">
                        <h3 class="plan-name">الخطة الأساسية</h3>
                        <p class="plan-subtitle">للمطاعم الصغيرة</p>
                    </div>
                    <div class="plan-price">
                        <span class="currency">د.إ</span>
                        <span class="price-value" data-monthly="29" data-yearly="290">29</span>
                        <span class="billing-cycle">/شهرياً</span>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 100 محادثة AI شهرياً</li>
                        <li><span class="check-icon">✓</span> قناة واحدة (واتساب)</li>
                        <li><span class="check-icon">✓</span> تحليلات أساسية</li>
                        <li><span class="check-icon">✓</span> دعم عبر البريد</li>
                    </ul>
                    <a href="{{ route('register') }}" class="cta-button outline">ابدأ مجاناً</a>
                </div>

                <div class="pricing-card card recommended" data-plan-id="professional">
                    <div class="plan-badge">الأكثر شعبية</div>
                    <div class="plan-header">
                        <h3 class="plan-name">الخطة الاحترافية</h3>
                        <p class="plan-subtitle">للمطاعم المتنامية</p>
                    </div>
                    <div class="plan-price">
                        <span class="currency">د.إ</span>
                        <span class="price-value" data-monthly="79" data-yearly="790">79</span>
                        <span class="billing-cycle">/شهرياً</span>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> 1000 محادثة AI شهرياً</li>
                        <li><span class="check-icon">✓</span> قنوات متعددة</li>
                        <li><span class="check-icon">✓</span> تحليلات متقدمة</li>
                        <li><span class="check-icon">✓</span> دعم ذو أولوية</li>
                        <li><span class="check-icon">✓</span> تكامل POS</li>
                    </ul>
                    <a href="{{ route('register') }}" class="cta-button primary">اختر هذه الخطة</a>
                </div>

                <div class="pricing-card card" data-plan-id="enterprise">
                    <div class="plan-header">
                        <h3 class="plan-name">خطة المؤسسات</h3>
                        <p class="plan-subtitle">للسلاسل الكبيرة</p>
                    </div>
                    <div class="plan-price">
                        <span class="price-value custom">مخصص</span>
                    </div>
                    <ul class="plan-features">
                        <li><span class="check-icon">✓</span> محادثات غير محدودة</li>
                        <li><span class="check-icon">✓</span> جميع الميزات</li>
                        <li><span class="check-icon">✓</span> وكلاء مخصصة</li>
                        <li><span class="check-icon">✓</span> دعم 24/7</li>
                        <li><span class="check-icon">✓</span> SLA مضمون</li>
                    </ul>
                    <a href="#contact-us" class="cta-button outline">تواصل معنا</a>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="faq-section" id="faq">
        <div class="container">
            <h2 class="section-title">الأسئلة الشائعة</h2>
            <p class="section-description">
                إجابات سريعة لأكثر الأسئلة شيوعاً
            </p>

            <div class="faq-accordion">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>كيف يعمل وكيل AI مع واتساب؟</span>
                        <span class="arrow-icon">▼</span>
                    </button>
                    <div class="faq-answer">
                        <p>نستخدم WhatsApp Business API الرسمي للتكامل المباشر. يستقبل الوكيل الرسائل، يفهمها، ويرد تلقائياً بناءً على قائمة طعامك وإعداداتك.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>هل يدعم اللغة العربية بشكل كامل؟</span>
                        <span class="arrow-icon">▼</span>
                    </button>
                    <div class="faq-answer">
                        <p>نعم، الوكيل مدرّب على اللغة العربية الفصحى والعامية الخليجية، مع دعم كامل للإنجليزية أيضاً.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>ماذا يحدث عند استفسار معقد؟</span>
                        <span class="arrow-icon">▼</span>
                    </button>
                    <div class="faq-answer">
                        <p>يحوّل الوكيل المحادثة تلقائياً لموظف بشري عند اكتشاف استفسارات معقدة أو عاطفية، مع تقديم ملخص كامل للمحادثة.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question">
                        <span>هل يمكن تجربة الخدمة قبل الاشتراك؟</span>
                        <span class="arrow-icon">▼</span>
                    </button>
                    <div class="faq-answer">
                        <p>بالتأكيد! نوفر تجربة مجانية لمدة 14 يوماً بدون الحاجة لبطاقة ائتمانية.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>جاهز لتحويل خدمة عملاء مطعمك؟</h2>
                <p>انضم لمئات المطاعم التي تستخدم Retorant AI</p>
                <a href="{{ route('register') }}" class="cta-button primary large">
                    <span>ابدأ تجربتك المجانية الآن</span>
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </a>
                <p class="cta-note">✓ بدون بطاقة ائتمانية  ✓ إلغاء في أي وقت</p>
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
                    <p>منصة ذكاء اصطناعي رائدة لأتمتة خدمة عملاء المطاعم في منطقة الخليج</p>
                    <div class="social-icons">
                        <a href="#" aria-label="Facebook"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg></a>
                        <a href="#" aria-label="Twitter"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/></svg></a>
                        <a href="#" aria-label="Instagram"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
                        <a href="#" aria-label="LinkedIn"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg></a>
                    </div>
                </div>
                <div class="footer-links">
                    <h4>المنتج</h4>
                    <ul>
                        <li><a href="#features">الميزات</a></li>
                        <li><a href="#pricing">الأسعار</a></li>
                        <li><a href="#how-it-works">كيف يعمل</a></li>
                        <li><a href="#faq">الأسئلة الشائعة</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>الشركة</h4>
                    <ul>
                        <li><a href="#about">من نحن</a></li>
                        <li><a href="#blog">المدونة</a></li>
                        <li><a href="#careers">الوظائف</a></li>
                        <li><a href="#contact">تواصل معنا</a></li>
                    </ul>
                </div>
                <div class="footer-links">
                    <h4>الدعم</h4>
                    <ul>
                        <li><a href="#help">مركز المساعدة</a></li>
                        <li><a href="#docs">التوثيق</a></li>
                        <li><a href="#privacy">سياسة الخصوصية</a></li>
                        <li><a href="#terms">الشروط والأحكام</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Retorant AI. جميع الحقوق محفوظة.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="{{ asset('js/landing.js') }}"></script>
</body>
</html>
