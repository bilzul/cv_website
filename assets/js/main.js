/**
 * CV Website Main JavaScript
 * Includes Font Awesome icon fixes and fallbacks (merged May 2025)
 */

// Self-executing function for Font Awesome icon fixes
(function() {
    console.log('Running icon fallback script - Merged May 2025');
    
    // Replace icons with text fallbacks - only removes duplicates
    function fixDuplicateIcons() {
        console.log('Fixing duplicate icons...');
        
        // Get all icon elements that might have duplicates
        const faIcons = document.querySelectorAll('.fa, .fab, .fas, .far, .fal, .fad');
        
        faIcons.forEach(function(icon) {
            // Skip icons that have aria-hidden attribute properly set
            if (icon.getAttribute('aria-hidden') === 'true') {
                return;
            }
            
            // Check if there's any text content that's likely from our fallback
            if (icon.textContent && icon.textContent.trim() !== '') {
                // Check if the text is already wrapped in a span
                const hasTextSpan = Array.from(icon.children).some(child => 
                    child.tagName === 'SPAN' && child.classList.contains('nav-text'));
                
                // If not wrapped and has direct text content, clear it
                if (!hasTextSpan && icon.childNodes.length > 0) {
                    // Clear just the text nodes
                    let node = icon.firstChild;
                    while (node) {
                        let nextNode = node.nextSibling;
                        if (node.nodeType === 3) { // Node.TEXT_NODE
                            node.remove();
                        }
                        node = nextNode;
                    }
                }
            }
        });
    }
      // Fix Font Awesome icons that aren't displaying correctly
    function fixFontAwesomeIcons() {
        console.log('Fixing icons...');
        
        // Get all icon elements
        const faIcons = document.querySelectorAll('.fa, .fab, .fas, .far, .fal, .fad');
        
        faIcons.forEach(function(icon) {
            // Skip icons that have proper aria-hidden attribute and are working correctly
            if (icon.getAttribute('aria-hidden') === 'true') {
                // Check for a sibling span that contains the text
                let nextSibling = icon.nextSibling;
                let hasTextSpan = false;
                
                while (nextSibling) {
                    if (nextSibling.nodeType === 1 && nextSibling.tagName === 'SPAN' && 
                        nextSibling.classList.contains('nav-text')) {
                        hasTextSpan = true;
                        break;
                    }
                    nextSibling = nextSibling.nextSibling;
                }
                
                // If properly structured with aria-hidden and text span, skip processing
                if (hasTextSpan) {
                    return;
                }
            }
            
            // First, clear any text content inside the icon (but not spans)
            if (icon.childNodes.length > 0) {
                // Clear any text nodes but leave other elements
                Array.from(icon.childNodes).forEach(node => {
                    if (node.nodeType === 3) { // Text node
                        node.textContent = '';
                    }
                });
            }
            
            // Check if icon is empty or not displaying correctly
            const computedStyle = window.getComputedStyle(icon, ':before');
            const contentValue = computedStyle.getPropertyValue('content');
            
            // If icon is either empty or has 'none' content, fix it
            if (contentValue === 'none' || (icon.innerHTML === '' && !icon.offsetWidth)) {
                console.log('Found problematic icon:', icon.className);
                
                // Add aria-hidden attribute if missing
                if (!icon.hasAttribute('aria-hidden')) {
                    icon.setAttribute('aria-hidden', 'true');
                }
                
                // If the icon has specific class for an icon, apply it properly
                const iconClasses = Array.from(icon.classList);
                let hasIconClass = false;
                
                iconClasses.forEach(cls => {
                    // Look for specific icon classes like "fa-home", "fa-user", etc.
                    if (cls.startsWith('fa-') && cls !== 'fa-solid' && cls !== 'fa-regular' && cls !== 'fa-brands') {
                        hasIconClass = true;
                        // No need to do anything, the class is already there
                    }
                });
                
                // If no specific icon class found, add a generic one
                if (!hasIconClass) {
                    console.warn('Icon missing specific class:', icon.className);
                }
            }
        });
    }
    
    // Run on DOM content loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Wait briefly to let Font Awesome initialize, then fix duplicates
        setTimeout(fixDuplicateIcons, 300);
        
        // Wait a bit longer for everything to load, then fix any remaining icon issues
        setTimeout(fixFontAwesomeIcons, 500);
    });
})();

document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Initialize Bootstrap tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Initialize Bootstrap popovers
  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
  popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl);
  });

  // Smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();

      const targetId = this.getAttribute('href');
      if (targetId === '#') return;

      const targetElement = document.querySelector(targetId);
      if (targetElement) {
        window.scrollTo({
          top: targetElement.offsetTop - 70,
          behavior: 'smooth'
        });
      }
    });
  });

  // Animate skill bars on page load or when they come into view
  const animateSkillBars = () => {
    document.querySelectorAll('.progress-bar').forEach(bar => {
      const value = bar.getAttribute('aria-valuenow');
      bar.style.width = '0%';
      setTimeout(() => {
        bar.style.width = value + '%';
      }, 100);
    });
  };

  // Run skill bar animation when skill section is in view
  const skillSection = document.querySelector('.skills-section');
  if (skillSection) {
    // Initial check
    if (isElementInViewport(skillSection)) {
      animateSkillBars();
    }
    
    // Check on scroll
    window.addEventListener('scroll', function() {
      if (isElementInViewport(skillSection)) {
        animateSkillBars();
      }
    });
  } else {
    // If we can't find skills section, just animate
    animateSkillBars();
  }
  // Add hover effect for timeline items
  document.querySelectorAll('.timeline-item').forEach(item => {
    // For desktop - use hover
    item.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-8px)';
      this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
    });

    item.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
    });
    
    // For mobile - use touch
    item.addEventListener('touchstart', function() {
      this.style.transform = 'translateY(-5px)';
      this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
    });
    
    item.addEventListener('touchend', function() {
      setTimeout(() => {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
      }, 200);
    });
  });
  
  // Close mobile nav when clicking outside
  document.addEventListener('click', function(event) {
    const navbar = document.getElementById('navbarNav');
    const navbarToggler = document.querySelector('.navbar-toggler');
    
    if (navbar && navbar.classList.contains('show') && 
        !navbar.contains(event.target) && 
        event.target !== navbarToggler) {
      // Using Bootstrap 5 collapse API
      const bsCollapse = new bootstrap.Collapse(navbar);
      bsCollapse.hide();
    }
  });
  
  // Automatically hide alerts after 5 seconds
  const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
  alerts.forEach(alert => {
    setTimeout(() => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }, 5000);
  });
  
  // Helper function to check if element is in viewport
  function isElementInViewport(el) {
    if (!el) return false;
    
    const rect = el.getBoundingClientRect();
    return (
      rect.top >= 0 &&
      rect.left >= 0 &&
      rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
  }
  // Add error handling for image loading
  document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function() {
      // Replace broken images with a placeholder
      if (!this.src.includes('placeholder.png')) {
        try {
          // Find the base URL by looking at CSS file location or using relative path
          const styleElement = document.querySelector('link[rel="stylesheet"][href*="/css/style.css"]');
          let imagePath = '/assets/images/placeholder.png'; // Default relative path
          
          if (styleElement && styleElement.href) {
            const assetUrl = styleElement.href.split('/css/')[0] || '';
            imagePath = assetUrl + '/images/placeholder.png';
          }
          
          this.src = imagePath;
          this.alt = 'Image could not be loaded';
          this.classList.add('broken-image');
        } catch (e) {
          console.error('Error setting placeholder image:', e);
        }
      }
    });
  });
  
  // Fix vh units on mobile browsers
  function fixVhUnits() {
    const vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  }
  
  fixVhUnits();
  window.addEventListener('resize', fixVhUnits);
  
  // Handle swipe for mobile touch interfaces
  let touchStartX = 0;
  let touchEndX = 0;
  
  document.addEventListener('touchstart', function(e) {
    touchStartX = e.changedTouches[0].screenX;
  }, false);
  
  document.addEventListener('touchend', function(e) {
    touchEndX = e.changedTouches[0].screenX;
    handleSwipe();
  }, false);
  
  function handleSwipe() {
    // Swipe distance threshold
    if (touchEndX < touchStartX - 100) {
      // Swiped left - do something here if needed
    }
    
    if (touchEndX > touchStartX + 100) {
      // Swiped right - close mobile nav menu if open
      const navbar = document.getElementById('navbarNav');
      if (navbar && navbar.classList.contains('show')) {
        const bsCollapse = new bootstrap.Collapse(navbar);
        bsCollapse.hide();
      }
    }
  }
  
  // Add hover effect for project cards
  document.querySelectorAll('.project-card').forEach(card => {
    card.addEventListener('mouseenter', function () {
      this.style.transform = 'translateY(-12px)';
      this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
    });

    card.addEventListener('mouseleave', function () {
      this.style.transform = 'translateY(0)';
      this.style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
    });
  });

  // Add custom fade-in animation style for all pages
  const addFadeInStyle = () => {
    if (!document.getElementById('fade-in-style')) {
      const style = document.createElement('style');
      style.id = 'fade-in-style';
      style.innerHTML = `
        .fade-in-element {
          animation: fadeIn 0.8s ease-in-out forwards;
        }
        @keyframes fadeIn {
          0% { opacity: 0; transform: translateY(20px); }
          100% { opacity: 1; transform: translateY(0); }
        }
      `;
      document.head.appendChild(style);
    }
  };

  // Apply custom animations to all pages
  const applyCustomAnimations = () => {
    // Apply to all main content elements on all pages
    const selectors = [
      '.profile-section',
      '.timeline-item',
      '.skill-item',
      '.project-card',
      '.contact-info',
      '.contact-item',
      '.admin-card',
      'h2:not(.navbar h2):not(.footer-main h2)',
      'h3:not(.navbar h3):not(.footer-main h3)'
    ];

    document.querySelectorAll(selectors.join(',')).forEach(el => {
      // Don't add animation to child elements of already animated containers
      const isChildOfAnimated = el.closest('.fade-in-element') !== null;
      if (!isChildOfAnimated && !el.classList.contains('fade-in-element')) {
        el.classList.add('fade-in-element');
      }
    });
  };

  // Enhanced Navbar Effects
  const enhanceNavbar = () => {
    const navbar = document.querySelector('.navbar');

    if (navbar) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
          navbar.classList.add('navbar-scrolled');
        } else {
          navbar.classList.remove('navbar-scrolled');
        }
      });
    }

    // Add subtle hover animation to nav links
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('mouseenter', function () {
        this.style.transform = 'translateY(-2px)';
      });

      link.addEventListener('mouseleave', function () {
        this.style.transform = 'translateY(0px)';
      });
    });
  };

  // Typed.js integration for dynamic text if library exists
  if (typeof Typed !== 'undefined') {
    const typedElements = document.querySelectorAll('.typed-element');

    typedElements.forEach(element => {
      const strings = element.getAttribute('data-typed-strings');
      if (strings) {
        new Typed(element, {
          strings: strings.split(','),
          typeSpeed: 70,
          backSpeed: 40,
          backDelay: 1500,
          startDelay: 500,
          loop: true
        });
      }
    });
  }

  // Call functions
  animateSkillBars();
  addFadeInStyle();
  applyCustomAnimations(); // Apply custom animations to all pages
  enhanceNavbar();
  // Handle delete confirmation
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function (e) {
      const confirmMessage = this.getAttribute('data-confirm') || 'Та энэ бичлэгийг устгахдаа итгэлтэй байна уу?';
      if (!confirm(confirmMessage)) {
        e.preventDefault();
      }
    });
  });

  // Handle nav links active state
  const navLinks = document.querySelectorAll('.nav-link');
  navLinks.forEach(link => {
    if (link.getAttribute('href') === window.location.pathname + window.location.search) {
      link.classList.add('active');
    }
  });

  // Add CSS variable style for navbar scrolled effect
  const addNavbarScrolledStyle = () => {
    if (!document.getElementById('navbar-scrolled-style')) {
      const style = document.createElement('style');
      style.id = 'navbar-scrolled-style';
      style.innerHTML = `
        .navbar-scrolled {
          padding: 0.5rem 0;
          box-shadow: 0 5px 15px rgba(0,0,0,0.1);
          background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
          transition: all 0.3s ease;
        }
      `;
      document.head.appendChild(style);
    }
  };

  addNavbarScrolledStyle();

  // Back to top button
  const createBackToTopButton = () => {
    const button = document.createElement('button');
    button.innerHTML = '<i class="fas fa-arrow-up"></i>';
    button.className = 'back-to-top';
    document.body.appendChild(button);

    window.addEventListener('scroll', () => {
      if (window.pageYOffset > 300) {
        button.classList.add('show');
      } else {
        button.classList.remove('show');
      }
    });

    button.addEventListener('click', () => {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
  };

  createBackToTopButton();

  // Add style for back to top button if not in CSS
  const addBackToTopStyle = () => {
    if (!document.getElementById('back-to-top-style')) {
      const style = document.createElement('style');
      style.id = 'back-to-top-style';
      style.innerHTML = `
        .back-to-top {
          position: fixed;
          bottom: 30px;
          right: 30px;
          width: 45px;
          height: 45px;
          background: var(--primary-color);
          color: white;
          border: none;
          border-radius: 50%;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          opacity: 0;
          visibility: hidden;
          transition: all 0.3s ease;
          z-index: 1000;
          box-shadow: 0 4px 12px rgba(78, 108, 255, 0.3);
        }
        .back-to-top:hover {
          background: var(--primary-dark);
          transform: translateY(-3px);
          box-shadow: 0 6px 15px rgba(78, 108, 255, 0.4);
        }
        .back-to-top.show {
          opacity: 1;
          visibility: visible;
        }
      `;
      document.head.appendChild(style);
    }
  };

  addBackToTopStyle();
});