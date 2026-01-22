// NINEVENTORY - Landing Page JavaScript

// Smooth Scroll for Navigation Links
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

// Navbar Scroll Effect - Transparent to Solid
const navbar = document.getElementById('navbar');
const navbarInner = document.getElementById('navbarInner');

function updateNavbar() {
    const scrolled = window.scrollY;

    if (scrolled > 50) {
        // Scrolled state - solid background
        navbarInner.classList.remove('border-white/0', 'bg-black/0', 'backdrop-blur-none', 'shadow-none');
        navbarInner.classList.add('border-white/10', 'bg-black/80', 'backdrop-blur-xl', 'shadow-lg');
    } else {
        // Top of page - transparent
        navbarInner.classList.remove('border-white/10', 'bg-black/80', 'backdrop-blur-xl', 'shadow-lg');
        navbarInner.classList.add('border-white/0', 'bg-black/0', 'backdrop-blur-none', 'shadow-none');
    }
}

// Listen for scroll events
window.addEventListener('scroll', updateNavbar);

// Initialize on page load
updateNavbar();

// Fade-in animations on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe all sections
document.querySelectorAll('section').forEach(section => {
    section.style.opacity = '0';
    section.style.transform = 'translateY(20px)';
    section.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
    observer.observe(section);
});

// Hero section should be visible immediately
const heroSection = document.getElementById('hero');
if (heroSection) {
    heroSection.style.opacity = '1';
    heroSection.style.transform = 'translateY(0)';
}
