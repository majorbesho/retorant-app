// ============================================
// RETORANT AI - LANDING PAGE INTERACTIONS
// ============================================

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

// ============================================
// HEADER SCROLL EFFECT
// ============================================
const header = document.getElementById('main-header');
let lastScroll = 0;

window.addEventListener('scroll', () => {
    const currentScroll = window.pageYOffset;

    if (currentScroll > 100) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }

    lastScroll = currentScroll;
});

// ============================================
// MOBILE MENU TOGGLE
// ============================================
const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
const mainNav = document.querySelector('.main-nav');

if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', () => {
        mainNav.classList.toggle('active');
        mobileMenuToggle.classList.toggle('active');
    });
}

// ============================================
// THREE.JS PARTICLE BACKGROUND
// ============================================
let scene, camera, renderer, particles, particleSystem;

function initThreeJS() {
    const canvas = document.getElementById('hero-canvas');
    if (!canvas) return;

    // Scene setup
    scene = new THREE.Scene();
    camera = new THREE.PerspectiveCamera(
        75,
        window.innerWidth / window.innerHeight,
        0.1,
        1000
    );

    renderer = new THREE.WebGLRenderer({
        canvas: canvas,
        alpha: true,
        antialias: true
    });

    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));

    camera.position.z = 50;

    // Create particles
    const particleCount = 1500;
    const positions = new Float32Array(particleCount * 3);
    const colors = new Float32Array(particleCount * 3);
    const sizes = new Float32Array(particleCount);

    const color1 = new THREE.Color(0x0066FF);
    const color2 = new THREE.Color(0x6C63FF);
    const color3 = new THREE.Color(0x00D9A3);

    for (let i = 0; i < particleCount; i++) {
        // Random positions
        positions[i * 3] = (Math.random() - 0.5) * 200;
        positions[i * 3 + 1] = (Math.random() - 0.5) * 200;
        positions[i * 3 + 2] = (Math.random() - 0.5) * 200;

        // Random colors (mix of brand colors)
        const mixedColor = Math.random();
        let finalColor;

        if (mixedColor < 0.33) {
            finalColor = color1;
        } else if (mixedColor < 0.66) {
            finalColor = color2;
        } else {
            finalColor = color3;
        }

        colors[i * 3] = finalColor.r;
        colors[i * 3 + 1] = finalColor.g;
        colors[i * 3 + 2] = finalColor.b;

        // Random sizes
        sizes[i] = Math.random() * 2;
    }

    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));
    geometry.setAttribute('size', new THREE.BufferAttribute(sizes, 1));

    const material = new THREE.PointsMaterial({
        size: 0.8,
        vertexColors: true,
        transparent: true,
        opacity: 0.6,
        blending: THREE.AdditiveBlending
    });

    particleSystem = new THREE.Points(geometry, material);
    scene.add(particleSystem);

    // Animation loop
    animate();
}

function animate() {
    requestAnimationFrame(animate);

    if (particleSystem) {
        particleSystem.rotation.x += 0.0003;
        particleSystem.rotation.y += 0.0005;

        // Drift particles
        const positions = particleSystem.geometry.attributes.position.array;
        for (let i = 0; i < positions.length; i += 3) {
            positions[i + 1] -= 0.02;

            if (positions[i + 1] < -100) {
                positions[i + 1] = 100;
            }
        }
        particleSystem.geometry.attributes.position.needsUpdate = true;
    }

    renderer.render(scene, camera);
}

function onWindowResize() {
    if (!camera || !renderer) return;

    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
}

window.addEventListener('resize', onWindowResize);

// Initialize Three.js when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    initThreeJS();
});

// ============================================
// GSAP ANIMATIONS
// ============================================

// Hero Section Animations
gsap.from('.main-header', {
    y: -100,
    opacity: 0,
    duration: 1,
    ease: 'power3.out'
});

gsap.from('.hero-badge', {
    scale: 0,
    opacity: 0,
    duration: 0.6,
    ease: 'back.out(1.7)',
    delay: 0.3
});

gsap.from('.hero-headline span', {
    y: 50,
    opacity: 0,
    stagger: 0.2,
    duration: 1,
    ease: 'power3.out',
    delay: 0.5
});

gsap.from('.hero-subheadline', {
    y: 30,
    opacity: 0,
    duration: 1,
    ease: 'power3.out',
    delay: 1
});

gsap.from('.hero-ctas .cta-button', {
    y: 30,
    opacity: 0,
    stagger: 0.2,
    duration: 0.8,
    ease: 'power3.out',
    delay: 1.3
});

gsap.from('.stat-item', {
    scale: 0,
    opacity: 0,
    stagger: 0.15,
    duration: 0.6,
    ease: 'back.out(1.7)',
    delay: 1.6
});

gsap.from('.scroll-indicator', {
    opacity: 0,
    duration: 1,
    ease: 'power2.out',
    delay: 2
});

// Scroll-triggered animations
const animateOnScroll = (selector, options = {}) => {
    const defaults = {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: 'power3.out',
        scrollTrigger: {
            trigger: selector,
            start: 'top 80%',
            toggleActions: 'play none none none'
        }
    };

    gsap.from(selector, { ...defaults, ...options });
};

// Problem-Solution Section
animateOnScroll('.problem-solution-section .section-title');
animateOnScroll('.problem-solution-section .section-description', { delay: 0.2 });
animateOnScroll('.problem-card', { delay: 0.4, x: -50 });
animateOnScroll('.solution-card', { delay: 0.6, x: 50 });

// Features Section
animateOnScroll('.features-in-action-section .section-title');
animateOnScroll('.features-in-action-section .section-description', { delay: 0.2 });

gsap.from('.feature-item', {
    y: 80,
    opacity: 0,
    stagger: 0.15,
    duration: 0.8,
    ease: 'power3.out',
    scrollTrigger: {
        trigger: '.feature-showcase-grid',
        start: 'top 75%'
    }
});

// How It Works Section
animateOnScroll('.how-it-works-section .section-title');
animateOnScroll('.how-it-works-section .section-description', { delay: 0.2 });

gsap.from('.step-item', {
    scale: 0.8,
    opacity: 0,
    stagger: 0.2,
    duration: 0.8,
    ease: 'back.out(1.7)',
    scrollTrigger: {
        trigger: '.steps-container',
        start: 'top 75%'
    }
});

// Testimonials Section
animateOnScroll('.testimonials-section .section-title');
animateOnScroll('.testimonials-section .section-description', { delay: 0.2 });

gsap.from('.testimonial-item', {
    y: 60,
    opacity: 0,
    stagger: 0.2,
    duration: 0.8,
    ease: 'power3.out',
    scrollTrigger: {
        trigger: '.testimonials-carousel',
        start: 'top 75%'
    }
});

// Pricing Section
animateOnScroll('.pricing-section .section-title');
animateOnScroll('.pricing-section .section-description', { delay: 0.2 });
animateOnScroll('.pricing-toggle', { delay: 0.4, scale: 0.9 });

gsap.from('.pricing-card', {
    y: 80,
    opacity: 0,
    stagger: 0.15,
    duration: 0.8,
    ease: 'power3.out',
    scrollTrigger: {
        trigger: '.pricing-cards-grid',
        start: 'top 75%'
    }
});

// FAQ Section
animateOnScroll('.faq-section .section-title');
animateOnScroll('.faq-section .section-description', { delay: 0.2 });

gsap.from('.faq-item', {
    x: -30,
    opacity: 0,
    stagger: 0.1,
    duration: 0.6,
    ease: 'power2.out',
    scrollTrigger: {
        trigger: '.faq-accordion',
        start: 'top 75%'
    }
});

// CTA Section
animateOnScroll('.cta-section h2');
animateOnScroll('.cta-section p', { delay: 0.2 });
animateOnScroll('.cta-section .cta-button', { delay: 0.4, scale: 0.9 });

// Footer
animateOnScroll('.main-footer', { y: 30, duration: 0.8 });

// ============================================
// PRICING TOGGLE FUNCTIONALITY
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('.pricing-toggle .toggle-button');
    const priceValues = document.querySelectorAll('.pricing-card .price-value');
    const billingCycles = document.querySelectorAll('.pricing-card .billing-cycle');

    toggleButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Update active state
            toggleButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');

            const cycle = button.dataset.billingCycle;

            // Animate price changes
            priceValues.forEach(priceSpan => {
                if (priceSpan.classList.contains('custom')) return;

                const newPrice = priceSpan.dataset[cycle];
                const currentPrice = parseInt(priceSpan.textContent);

                gsap.to(priceSpan, {
                    textContent: newPrice,
                    duration: 0.5,
                    ease: 'power2.inOut',
                    snap: { textContent: 1 },
                    onUpdate: function () {
                        priceSpan.textContent = Math.round(this.targets()[0].textContent);
                    }
                });
            });

            // Update billing cycle text
            billingCycles.forEach(cycleSpan => {
                cycleSpan.textContent = cycle === 'monthly' ? '/شهرياً' : '/سنوياً';
            });
        });
    });
});

// ============================================
// FAQ ACCORDION FUNCTIONALITY
// ============================================
document.addEventListener('DOMContentLoaded', () => {
    const faqQuestions = document.querySelectorAll('.faq-question');

    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.closest('.faq-item');
            const faqAnswer = faqItem.querySelector('.faq-answer');
            const isOpen = faqItem.classList.contains('open');

            // Close all other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) {
                    item.classList.remove('open');
                    item.querySelector('.faq-question').classList.remove('active');
                    item.querySelector('.faq-answer').style.maxHeight = '0';
                }
            });

            // Toggle current FAQ
            if (isOpen) {
                faqItem.classList.remove('open');
                question.classList.remove('active');
                faqAnswer.style.maxHeight = '0';
            } else {
                faqItem.classList.add('open');
                question.classList.add('active');
                faqAnswer.style.maxHeight = faqAnswer.scrollHeight + 'px';
            }
        });
    });
});

// ============================================
// SMOOTH SCROLL FOR ANCHOR LINKS
// ============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#' || href === '#!') return;

        e.preventDefault();
        const target = document.querySelector(href);

        if (target) {
            const headerOffset = 80;
            const elementPosition = target.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// ============================================
// BUTTON RIPPLE EFFECT
// ============================================
document.querySelectorAll('.cta-button').forEach(button => {
    button.addEventListener('click', function (e) {
        const rect = this.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.6);
            transform: translate(-50%, -50%);
            pointer-events: none;
            animation: ripple-animation 0.6s ease-out;
        `;
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';

        this.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    });
});

// Add ripple animation to stylesheet dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple-animation {
        to {
            width: 200px;
            height: 200px;
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// ============================================
// INTERSECTION OBSERVER FOR LAZY ANIMATIONS
// ============================================
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

// Observe all cards
document.querySelectorAll('.card').forEach(card => {
    observer.observe(card);
});

// ============================================
// PERFORMANCE OPTIMIZATION
// ============================================

// Debounce function for scroll events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Optimize scroll listeners
window.addEventListener('scroll', debounce(() => {
    // Any additional scroll-based logic
}, 100));

// ============================================
// CONSOLE EASTER EGG
// ============================================
console.log('%c🤖 Retorant AI', 'font-size: 24px; font-weight: bold; color: #0066FF;');
console.log('%cمهتم بالتكنولوجيا؟ انضم لفريقنا!', 'font-size: 14px; color: #6C63FF;');
console.log('%cVisit: careers@retorant.ai', 'font-size: 12px; color: #00D9A3;');

// ============================================
// ANALYTICS & TRACKING (Placeholder)
// ============================================
function trackEvent(category, action, label) {
    // Integrate with Google Analytics, Mixpanel, etc.
    console.log('Event:', category, action, label);
}

// Track CTA clicks
document.querySelectorAll('.cta-button').forEach(button => {
    button.addEventListener('click', () => {
        const buttonText = button.textContent.trim();
        trackEvent('CTA', 'Click', buttonText);
    });
});

// Track pricing plan selections
document.querySelectorAll('.pricing-card .cta-button').forEach(button => {
    button.addEventListener('click', () => {
        const planName = button.closest('.pricing-card').querySelector('.plan-name').textContent;
        trackEvent('Pricing', 'Plan Selected', planName);
    });
});
