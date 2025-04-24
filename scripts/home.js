document.addEventListener('DOMContentLoaded', function() {
  // Set current year and last modified date
  const currentYear = new Date().getFullYear();
  document.getElementById('current-year').textContent = currentYear;
  
  const lastModified = document.lastModified;
  document.getElementById('last-modified').textContent = lastModified;
  
  // Mobile navigation toggle
  const navToggle = document.querySelector('.nav-toggle');
  const nav = document.querySelector('.nav');
  
  navToggle.addEventListener('click', function() {
      navToggle.classList.toggle('active');
      nav.classList.toggle('active');
  });
  
  // Close mobile menu when clicking on a link
  const navLinks = document.querySelectorAll('.nav-item a');
  navLinks.forEach(link => {
      link.addEventListener('click', function() {
          if (nav.classList.contains('active')) {
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
  
  nextBtn.addEventListener('click', nextTestimonial);
  prevBtn.addEventListener('click', prevTestimonial);
  
  // Auto-advance testimonials
  let testimonialInterval = setInterval(nextTestimonial, 5000);
  
  // Pause auto-advance on hover
  const slider = document.querySelector('.testimonial-slider');
  slider.addEventListener('mouseenter', () => clearInterval(testimonialInterval));
  slider.addEventListener('mouseleave', () => {
      testimonialInterval = setInterval(nextTestimonial, 5000);
  });
  
  // Newsletter form
  const newsletterForm = document.getElementById('newsletter-form');
  const newsletterMessage = document.getElementById('newsletter-message');
  
  newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const email = document.getElementById('newsletter-email').value;
      
      // Simple validation
      if (!email.includes('@') || !email.includes('.')) {
          newsletterMessage.textContent = 'Please enter a valid email address.';
          newsletterMessage.classList.add('error');
          newsletterMessage.classList.remove('success');
          newsletterMessage.style.display = 'block';
          return;
      }
      
      // Save to localStorage
      let subscribers = JSON.parse(localStorage.getItem('newsletterSubscribers') || []);
      
      // Check if email already exists
      if (subscribers.includes(email)) {
          newsletterMessage.textContent = 'You are already subscribed!';
          newsletterMessage.classList.add('error');
          newsletterMessage.classList.remove('success');
          newsletterMessage.style.display = 'block';
          return;
      }
      
      subscribers.push(email);
      localStorage.setItem('newsletterSubscribers', JSON.stringify(subscribers));
      
      // Show success message
      newsletterMessage.textContent = 'Thank you for subscribing!';
      newsletterMessage.classList.add('success');
      newsletterMessage.classList.remove('error');
      newsletterMessage.style.display = 'block';
      
      // Reset form
      newsletterForm.reset();
      
      // Hide message after 5 seconds
      setTimeout(() => {
          newsletterMessage.style.display = 'none';
      }, 5000);
  });
  
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
      });
      
      lazyImages.forEach(img => {
          if (img.complete) return;
          imageObserver.observe(img);
      });
  }
});