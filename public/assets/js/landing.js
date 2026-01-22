// NINEVENTORY Landing Page - JavaScript

// Animation state
let isMounted = false;

// Pillar heights for hero section (symmetric pattern)
const pillars = [92, 84, 78, 70, 62, 54, 46, 34, 18, 34, 46, 54, 62, 70, 78, 84, 92];

// Initialize animations on page load
document.addEventListener('DOMContentLoaded', function () {
    // Trigger mount animations after short delay
    setTimeout(() => {
        isMounted = true;
        triggerAnimations();
    }, 100);
});

// Trigger all animations
function triggerAnimations() {
    // Fade in hero content
    const heroElements = document.querySelectorAll('.hero-animate');
    heroElements.forEach((el, index) => {
        setTimeout(() => {
            el.classList.add('animate-fadeInUp');
            el.style.opacity = '1';
        }, index * 200);
    });

    // Animate pillars
    animatePillars();
}

// Animate hero pillars
function animatePillars() {
    const pillarElements = document.querySelectorAll('.pillar');
    pillarElements.forEach((pillar, index) => {
        const targetHeight = pillars[index];
        const centerIndex = Math.floor(pillars.length / 2);
        const delay = Math.abs(index - centerIndex) * 60;

        setTimeout(() => {
            pillar.style.height = `${targetHeight}%`;
        }, delay);
    });
}

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

// Add scroll reveal for features
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fadeInUp');
            entry.target.style.opacity = '1';
        }
    });
}, observerOptions);

// Observe feature cards
document.addEventListener('DOMContentLoaded', () => {
    const features = document.querySelectorAll('.feature-card');
    features.forEach(feature => {
        observer.observe(feature);
    });
});

// Navbar scroll behavior - Always visible with enhanced styling on scroll
let lastScrollTop = 0;
const navbar = document.querySelector('header');

window.addEventListener('scroll', () => {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // Always keep navbar visible, just enhance styling when scrolled
    if (scrollTop > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }

    lastScrollTop = scrollTop;
}, { passive: true });
