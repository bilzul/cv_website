/**
 * Fallback JavaScript - Provides basic functionality when main.js fails to load
 */

(function() {
    console.log('Using fallback.js - basic functionality only');
    
    // Basic broken image handling
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('img').forEach(function(img) {
            img.addEventListener('error', function() {
                this.src = '/assets/images/placeholder.png';
                this.alt = 'Image could not be loaded';
                this.classList.add('broken-image');
            });
        });
        
        // Basic mobile menu toggle
        const menuToggle = document.querySelector('.navbar-toggler');
        const navMenu = document.querySelector('#navbarNav');
        
        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', function() {
                if (navMenu.classList.contains('show')) {
                    navMenu.classList.remove('show');
                } else {
                    navMenu.classList.add('show');
                }
            });
        }
        
        // Simple form validation
        const forms = document.querySelectorAll('form');
        forms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                let hasError = false;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        hasError = true;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });
                
                if (hasError) {
                    e.preventDefault();
                    alert('Please fill in all required fields');
                }
            });
        });
    });
})();
