document.addEventListener('DOMContentLoaded', function() {
    // Set current year and last modified date
    const currentYear = new Date().getFullYear();
    document.getElementById('current-year').textContent = currentYear;
    document.getElementById('last-modified').textContent = document.lastModified;

    // Mobile navigation toggle
    const navToggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.nav');

    if (navToggle && nav) {
        navToggle.addEventListener('click', function() {
            navToggle.classList.toggle('active');
            nav.classList.toggle('active');
        });
    }

    // Close mobile menu when clicking on a link
    const navLinks = document.querySelectorAll('.nav-item a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (nav && nav.classList.contains('active')) {
                navToggle.classList.remove('active');
                nav.classList.remove('active');
            }
        });
    });

    // Testimonial slider
    const testimonials = document.querySelectorAll('.testimonial');
    const dotsContainer = document.querySelector('.slider-dots');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    
    if (testimonials.length > 0 && dotsContainer) {
        let currentIndex = 0;
        
        // Create dots
        testimonials.forEach((_, index) => {
            const dot = document.createElement('span');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToTestimonial(index));
            dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.dot');

        function updateTestimonial() {
            testimonials.forEach((testimonial, index) => {
                testimonial.classList.toggle('active', index === currentIndex);
            });

            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentIndex);
            });
        }

        function goToTestimonial(index) {
            currentIndex = index;
            updateTestimonial();
        }

        function nextTestimonial() {
            currentIndex = (currentIndex + 1) % testimonials.length;
            updateTestimonial();
        }

        function prevTestimonial() {
            currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
            updateTestimonial();
        }

        if (nextBtn) nextBtn.addEventListener('click', nextTestimonial);
        if (prevBtn) prevBtn.addEventListener('click', prevTestimonial);

        // Auto-advance testimonials
        let testimonialInterval = setInterval(nextTestimonial, 5000);

        // Pause auto-advance on hover
        const slider = document.querySelector('.testimonial-slider');
        if (slider) {
            slider.addEventListener('mouseenter', () => clearInterval(testimonialInterval));
            slider.addEventListener('mouseleave', () => {
                testimonialInterval = setInterval(nextTestimonial, 5000);
            });
        }
    }

    // Newsletter form
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        const newsletterMessage = document.getElementById('newsletter-message');
        
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('newsletter-email').value;

            // Simple validation
            if (!email.includes('@') || !email.includes('.')) {
                showFormMessage(newsletterMessage, 'Please enter a valid email address.', 'error');
                return;
            }

            // Save to localStorage
            try {
                let subscribers = JSON.parse(localStorage.getItem('newsletterSubscribers') || '[]');

                // Check if email already exists
                if (subscribers.includes(email)) {
                    showFormMessage(newsletterMessage, 'You are already subscribed!', 'error');
                    return;
                }

                subscribers.push(email);
                localStorage.setItem('newsletterSubscribers', JSON.stringify(subscribers));

                // Show success message
                showFormMessage(newsletterMessage, 'Thank you for subscribing!', 'success');
                newsletterForm.reset();

                // Hide message after 5 seconds
                setTimeout(() => {
                    if (newsletterMessage) newsletterMessage.style.display = 'none';
                }, 5000);
            } catch (error) {
                showFormMessage(newsletterMessage, 'An error occurred. Please try again.', 'error');
                console.error('Error saving to localStorage:', error);
            }
            // DELETE FROM HERE AND GOES DOWN
            // Newsletter email notification functionality
(function() {
    const newsletterForm = document.getElementById('newsletter-form');
    if (!newsletterForm) return;

    // Initialize EmailJS (you'll need to sign up at https://www.emailjs.com/)
    // Replace these with your actual EmailJS credentials
    const EMAILJS_SERVICE_ID = 'service_astczr8';
    const EMAILJS_TEMPLATE_ID = 'template_5d4zcay';
    const EMAILJS_USER_ID = 'DWiVateh9nLzWFFCb';
    const YOUR_EMAIL = 'marcos@marcosmurilocampos.com'; // Where you want to receive notifications
    
    // Load EmailJS library dynamically
    const loadEmailJS = () => {
        return new Promise((resolve, reject) => {
            if (window.emailjs) {
                resolve(window.emailjs);
                return;
            }
            
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js';
            script.onload = () => {
                emailjs.init(EMAILJS_USER_ID);
                resolve(window.emailjs);
            };
            script.onerror = reject;
            document.head.appendChild(script);
        });
    };

    // Modified newsletter form submission handler
    const originalSubmitHandler = newsletterForm._submitHandler || newsletterForm.onsubmit;
    
    newsletterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const email = document.getElementById('newsletter-email').value;
        const newsletterMessage = document.getElementById('newsletter-message');

        // First run the original validation and storage logic
        if (originalSubmitHandler) {
            originalSubmitHandler.call(this, e);
        } else {
            // Fallback to default behavior if no original handler
            if (!email.includes('@') || !email.includes('.')) {
                showFormMessage(newsletterMessage, 'Please enter a valid email address.', 'error');
                return;
            }
            
            try {
                let subscribers = JSON.parse(localStorage.getItem('newsletterSubscribers') || '[]');
                if (subscribers.includes(email)) {
                    showFormMessage(newsletterMessage, 'You are already subscribed!', 'error');
                    return;
                }
                subscribers.push(email);
                localStorage.setItem('newsletterSubscribers', JSON.stringify(subscribers));
            } catch (error) {
                console.error('Error saving to localStorage:', error);
            }
        }

        // Then send the email notification
        try {
            await loadEmailJS();
            await emailjs.send(EMAILJS_SERVICE_ID, EMAILJS_TEMPLATE_ID, {
                to_email: YOUR_EMAIL,
                subscriber_email: email,
                date: new Date().toLocaleString()
            });
            
            console.log('Subscription notification email sent successfully');
        } catch (emailError) {
            console.error('Failed to send subscription notification:', emailError);
            // Don't show this error to the user - the subscription still worked
        }

        // Show success message if not already shown by original handler
        if (!newsletterMessage.textContent) {
            showFormMessage(newsletterMessage, 'Thank you for subscribing!', 'success');
            newsletterForm.reset();
            setTimeout(() => {
                if (newsletterMessage) newsletterMessage.style.display = 'none';
            }, 5000);
        }
    });

    // Preserve the original submit handler if it exists
    if (originalSubmitHandler) {
        newsletterForm._submitHandler = originalSubmitHandler;
    }
})(); // DELETE UNTIL HERE IF NEEDED
        });
    }

    // Contact form
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        const contactMessage = document.getElementById('contact-message');
        
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('contact-name').value;
            const email = document.getElementById('contact-email').value;
            const message = document.getElementById('contact-message-text').value;

            // Simple validation
            if (!name || !email || !message) {
                showFormMessage(contactMessage, 'Please fill out all fields.', 'error');
                return;
            }

            if (!email.includes('@') || !email.includes('.')) {
                showFormMessage(contactMessage, 'Please enter a valid email address.', 'error');
                return;
            }

            // Save contact message to localStorage
            try {
                let contactMessages = JSON.parse(localStorage.getItem('contactMessages') || '[]');
                contactMessages.push({
                    name,
                    email,
                    message,
                    date: new Date().toISOString()
                });
                localStorage.setItem('contactMessages', JSON.stringify(contactMessages));

                // Show success message
                showFormMessage(contactMessage, 'Thank you for your message! I will get back to you soon.', 'success');
                contactForm.reset();

                // Hide message after 5 seconds
                setTimeout(() => {
                    if (contactMessage) contactMessage.style.display = 'none';
                }, 5000);
            } catch (error) {
                showFormMessage(contactMessage, 'An error occurred. Please try again.', 'error');
                console.error('Error saving to localStorage:', error);
            }
        });
    }

    // FAQ accordion
    const faqItems = document.querySelectorAll('.faq-item');
    if (faqItems.length > 0) {
        faqItems.forEach(item => {
            const question = item.querySelector('.faq-question');
            if (question) {
                question.addEventListener('click', function() {
                    // Close all other items
                    faqItems.forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            const otherAnswer = otherItem.querySelector('.faq-answer');
                            if (otherAnswer) otherAnswer.style.maxHeight = null;
                        }
                    });

                    // Toggle current item
                    item.classList.toggle('active');
                    const answer = item.querySelector('.faq-answer');
                    if (answer) {
                        if (item.classList.contains('active')) {
                            answer.style.maxHeight = answer.scrollHeight + 'px';
                        } else {
                            answer.style.maxHeight = null;
                        }
                    }
                });
            }
        });
    }

    // Lazy loading for images
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: '200px'
        });

        lazyImages.forEach(img => {
            if (img.complete) return;
            imageObserver.observe(img);
        });
    }

    // Helper function to show form messages
    function showFormMessage(element, message, type) {
        if (!element) return;
        
        element.textContent = message;
        element.classList.remove('error', 'success');
        element.classList.add(type);
        element.style.display = 'block';
    }

    
    
});
app.post('/submit-form', async (req, res) => {
    const secret = "6Lfe4SYrAAAAAKwXhS-ojf8T85gEXR5s5GtnFAek";
    const response = await fetch(
        `https://www.google.com/recaptcha/api/siteverify?secret=${secret}&response=${req.body['g-recaptcha-response']}`,
        { method: 'POST' }
    );
    const data = await response.json();
    
    if (!data.success) {
        return res.status(400).send("reCAPTCHA failed");
    }
    // Process form data...
});
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contact-form');
    const formMessage = document.getElementById('contact-message');
    
    // Generate and set CSRF token
    const csrfToken = generateCsrfToken();
    document.getElementById('csrf_token').value = csrfToken;
    
    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        
        // Collect form data
        const formData = new FormData(contactForm);
        
        // Send via AJAX
        fetch('process_contact.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                formMessage.textContent = data.message;
                formMessage.className = 'form-message success';
                contactForm.reset();
            } else {
                formMessage.textContent = data.message;
                formMessage.className = 'form-message error';
            }
        })
        .catch(error => {
            formMessage.textContent = 'An error occurred. Please try again.';
            formMessage.className = 'form-message error';
            console.error('Error:', error);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Send Message';
        });
    });
    
    // Function to generate CSRF token
    function generateCsrfToken() {
        return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    }
});
document.getElementById('contact-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    const messageDiv = document.getElementById('contact-message');
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text) });
        }
        return response.json();
    })
    .then(data => {
        messageDiv.textContent = data.message;
        messageDiv.className = 'form-message success';
        form.reset();
        grecaptcha.reset();
    })
    .catch(error => {
        messageDiv.textContent = error.message;
        messageDiv.className = 'form-message error';
    });
});