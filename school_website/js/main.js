// Theme Persistence Logic (Immediate Execution)
(function () {
    const savedTheme = localStorage.getItem('theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);
})();

// Google Analytics Enhanced Tracking
function trackEvent(category, action, label = null, value = null) {
    if (typeof gtag !== 'undefined') {
        gtag('event', action, {
            'event_category': category,
            'event_label': label,
            'value': value
        });
    }
}

// Track form submissions
document.addEventListener('submit', function(e) {
    const form = e.target;
    const formName = form.getAttribute('name') || form.id || 'unnamed_form';
    
    // Track specific forms on the school website
    if(form.action.includes('inquiry') || form.action.includes('contact')) {
        trackEvent('School Forms', 'submit', formName);
    } else {
        trackEvent('Form', 'submit', formName);
    }
});

// Track button clicks
document.addEventListener('click', function(e) {
    const button = e.target.closest('button, a.btn, .btn');
    if (button) {
        const buttonText = button.textContent.trim() || button.getAttribute('aria-label') || 'unnamed_button';
        
        // Track important school actions
        if(buttonText.toLowerCase().includes('admission') || buttonText.toLowerCase().includes('apply')) {
            trackEvent('School Actions', 'click', 'Admission Application');
        } else if(buttonText.toLowerCase().includes('contact') || buttonText.toLowerCase().includes('message')) {
            trackEvent('School Actions', 'click', 'Contact Action');
        } else if(buttonText.toLowerCase().includes('download') || buttonText.toLowerCase().includes('curriculum')) {
            trackEvent('School Actions', 'click', 'Resource Download');
        } else {
            trackEvent('Button', 'click', buttonText.substring(0, 50));
        }
    }
});

// Track outbound links
document.addEventListener('click', function(e) {
    const link = e.target.closest('a');
    if(link && link.hostname && link.hostname !== window.location.hostname) {
        trackEvent('Outbound Link', 'click', link.href);
    }
});

// Track PDF downloads
document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href$=".pdf"]');
    if(link) {
        trackEvent('PDF Downloads', 'click', link.getAttribute('href'));
    }
});

// Track scroll depth
let scrollDepthTracked = { 25: false, 50: false, 75: false, 90: false };
window.addEventListener('scroll', function() {
    const scrollPercent = Math.round(100 * window.pageYOffset / (document.body.scrollHeight - window.innerHeight));
    
    Object.keys(scrollDepthTracked).forEach(depth => {
        if (!scrollDepthTracked[depth] && scrollPercent >= parseInt(depth)) {
            scrollDepthTracked[depth] = true;
            trackEvent('Scroll Depth', 'reach', `${depth}%`);
        }
    });
});

// Track page views for SPA-like navigation
function trackPageView(pageTitle, pagePath) {
    if (typeof gtag !== 'undefined') {
        gtag('config', window.GOOGLE_ANALYTICS_ID || 'GA_MEASUREMENT_ID', {
            'page_title': pageTitle,
            'page_location': window.location.origin + pagePath
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle Logic
    const themeToggle = document.getElementById('theme-toggle');
    const themeIcon = themeToggle?.querySelector('i');

    const updateThemeIcon = (theme) => {
        if (!themeIcon) return;
        if (theme === 'light') {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            themeToggle.style.color = '#f59e0b'; // Amber for sun
        } else {
            themeIcon.classList.replace('fa-sun', 'fa-moon');
            themeToggle.style.color = '#94a3b8';
        }
    };

    // Initial Icon Sync
    updateThemeIcon(document.documentElement.getAttribute('data-theme'));

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
        });
    }

    // Navbar Scroll Effect
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // Mobile Menu Toggle
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            navLinks.classList.toggle('active');
            const icon = mobileToggle.querySelector('i');
            if (navLinks.classList.contains('active')) {
                icon.classList.replace('fa-bars', 'fa-times');
                document.body.style.overflow = 'hidden'; // Lock scroll
            } else {
                icon.classList.replace('fa-times', 'fa-bars');
                document.body.style.overflow = '';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (navLinks.classList.contains('active') && !navLinks.contains(e.target) && !mobileToggle.contains(e.target)) {
                navLinks.classList.remove('active');
                const icon = mobileToggle.querySelector('i');
                icon.classList.replace('fa-times', 'fa-bars');
                document.body.style.overflow = '';
            }
        });
    }

    // Reveal on Scroll
    const reveals = document.querySelectorAll('.reveal');
    const revealOnScroll = () => {
        const windowHeight = window.innerHeight;
        reveals.forEach(reveal => {
            const revealTop = reveal.getBoundingClientRect().top;
            const revealPoint = 150;
            if (revealTop < windowHeight - revealPoint) {
                reveal.classList.add('active');
            }
        });
    };

    // Slider Logic
    let currentSlideIndex = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.slider-dot');
    const totalSlides = slides.length;

    function showSlide(index) {
        if (index >= totalSlides) currentSlideIndex = 0;
        else if (index < 0) currentSlideIndex = totalSlides - 1;
        else currentSlideIndex = index;

        const slider = document.querySelector('.hero-slider');
        if (slider) {
            slider.style.transform = `translateX(-${currentSlideIndex * 100}%)`;

            // Update dots
            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[currentSlideIndex]) dots[currentSlideIndex].classList.add('active');
        }
    }

    // Auto Slide
    let sliderInterval = setInterval(() => {
        showSlide(currentSlideIndex + 1);
    }, 5000);

    // Global helper for dots
    window.currentSlide = (index) => {
        clearInterval(sliderInterval);
        showSlide(index);
        sliderInterval = setInterval(() => {
            showSlide(currentSlideIndex + 1);
        }, 5000);
    };

    // Initialize reveal
    revealOnScroll();

    // Stats Counter Animation
    const counters = document.querySelectorAll('.counter');
    const speed = 200; // The lower the slower

    const animateCounter = (counter) => {
        const target = +counter.getAttribute('data-target');
        const count = +counter.innerText;
        const inc = target / speed;

        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(() => animateCounter(counter), 1);
        } else {
            counter.innerText = target;
        }
    };

    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counter = entry.target;
                animateCounter(counter);
                observer.unobserve(counter);
            }
        });
    }, {
        threshold: 0.5
    });

    counters.forEach(counter => counterObserver.observe(counter));
});
