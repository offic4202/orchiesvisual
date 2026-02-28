document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const lightbox = document.getElementById('videoLightbox');
    const lightboxIframe = document.getElementById('lightboxiframe');
    const lightboxClose = document.querySelector('.lightbox-close');
    const contactForm = document.getElementById('contactForm');
    const revealElements = document.querySelectorAll('.section-header, .about-content, .contact-info, .portfolio-item, .client-logo');

    navbar.classList.remove('scrolled');

    window.addEventListener('scroll', function() {
        if (window.scrollY > 100) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    hamburger.addEventListener('click', function() {
        navLinks.classList.toggle('active');
        hamburger.classList.toggle('active');
    });

    navLinks.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', function() {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
        });
    });

    document.querySelectorAll('.btn-play').forEach(btn => {
        btn.addEventListener('click', function() {
            const videoUrl = this.getAttribute('data-video');
            lightboxIframe.src = videoUrl + '?autoplay=1';
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    });

    lightboxClose.addEventListener('click', function() {
        lightbox.classList.remove('active');
        lightboxIframe.src = '';
        document.body.style.overflow = '';
    });

    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            lightbox.classList.remove('active');
            lightboxIframe.src = '';
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && lightbox.classList.contains('active')) {
            lightbox.classList.remove('active');
            lightboxIframe.src = '';
            document.body.style.overflow = '';
        }
    });

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const revealObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
            }
        });
    }, observerOptions);

    revealElements.forEach(el => {
        el.classList.add('reveal');
        revealObserver.observe(el);
    });

    document.querySelectorAll('.portfolio-item').forEach(item => {
        revealObserver.observe(item);
    });

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(contactForm);
        const name = formData.get('name');
        const email = formData.get('email');
        const subject = formData.get('subject');
        const message = formData.get('message');

        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        setTimeout(function() {
            submitBtn.textContent = 'Message Sent!';
            submitBtn.style.background = '#4CAF50';

            setTimeout(function() {
                submitBtn.textContent = originalText;
                submitBtn.style.background = '';
                submitBtn.disabled = false;
                contactForm.reset();
            }, 2000);
        }, 1000);
    });

    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(group => {
        const input = group.querySelector('input, textarea');
        if (input) {
            input.addEventListener('focus', function() {
                group.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                group.classList.remove('focused');
            });
        }
    });

    const skillItems = document.querySelectorAll('.skill-item');
    skillItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px)';
        });
        item.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });

    const statNumbers = document.querySelectorAll('.stat-number');
    statNumbers.forEach(stat => {
        const finalValue = stat.textContent;
        const numericValue = parseInt(finalValue.replace(/\D/g, ''));
        const suffix = finalValue.replace(/[0-9]/g, '');

        let current = 0;
        const increment = numericValue / 50;
        const observer = new IntersectionObserver(function(entries) {
            if (entries[0].isIntersecting) {
                const timer = setInterval(function() {
                    current += increment;
                    if (current >= numericValue) {
                        stat.textContent = finalValue;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current) + suffix;
                    }
                }, 30);
                observer.disconnect();
            }
        }, { threshold: 0.5 });
        observer.observe(stat);
    });

    const clientsSection = document.querySelector('.clients');
    if (clientsSection) {
        const slider = clientsSection.querySelector('.clients-slider');
        const track = clientsSection.querySelector('.clients-track');

        slider.addEventListener('mouseenter', function() {
            track.style.animationPlayState = 'paused';
        });

        slider.addEventListener('mouseleave', function() {
            track.style.animationPlayState = 'running';
        });
    }

    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
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

    const heroVideo = document.querySelector('.hero-video video');
    if (heroVideo) {
        heroVideo.play().catch(function() {
        });
    }

    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        if (img.complete) {
            img.style.opacity = '1';
        }
    });

    console.log('Orchies Visual Portfolio loaded successfully');

    const tabBtns = document.querySelectorAll('.tab-btn');
    const equipmentCards = document.querySelectorAll('.equipment-card');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const tabCategory = this.getAttribute('data-tab');

            equipmentCards.forEach(card => {
                const cardCategory = card.getAttribute('data-category');
                if (tabCategory === 'all' || cardCategory === tabCategory) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });

    const equipmentSection = document.querySelector('.equipment');
    if (equipmentSection) {
        revealObserver.observe(equipmentSection);
    }
});
