    </div>
    </main>

    <!-- Footer Section -->
    <footer class="footer-main">
        <div class="footer-content">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4 mb-4 mb-md-0">
                        <div class="footer-brand">
                            <h5 class="footer-logo mx-auto" style="color: white;">CV<span class="footer-dot"></span></h5>
                            <p class="footer-tagline">Програм хангамжийн инженерийн танилцуулга</p>
                        </div>
                        <div class="footer-social justify-content-center">
                            <a href="#" class="social-link" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="social-link" aria-label="GitHub"><i class="fab fa-github"></i></a>
                            <a href="#" class="social-link" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4 mb-md-0">
                        <h5 class="footer-heading mx-auto">Холбоосууд</h5>
                        <ul class="footer-links mx-auto">
                            <li><a href="index.php?page=home"><i class="fas fa-angle-right"></i> Нүүр</a></li>
                            <li><a href="index.php?page=education"><i class="fas fa-angle-right"></i> Боловсрол</a></li>
                            <li><a href="index.php?page=experience"><i class="fas fa-angle-right"></i> Туршлага</a></li>
                            <li><a href="index.php?page=skills"><i class="fas fa-angle-right"></i> Ур чадвар</a></li>
                            <li><a href="index.php?page=projects"><i class="fas fa-angle-right"></i> Төслүүд</a></li>
                            <li><a href="index.php?page=contact"><i class="fas fa-angle-right"></i> Холбоо барих</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5 class="footer-heading mx-auto">Холбоо барих</h5>
                        <div class="footer-contact mx-auto">
                            <p><i class="fas fa-envelope"></i> ubilguun@gmail.com</p>
                            <p><i class="fas fa-phone"></i> +976 99639641</p>
                            <p><i class="fas fa-map-marker-alt"></i> Улаанбаатар, Монгол</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center text-center">
                    <div class="col-12">
                        <p class="copyright mb-2 mb-md-2">© <?php echo date('Y'); ?> Бүх эрх хуулиар хамгаалагдсан</p>
                        <p class="footer-credit">Made with <i class="fas fa-heart text-danger"></i> in Mongolia</p>
                    </div>
                </div>
            </div>
        </div>
    </footer> <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script> <!-- Custom JS -->
    <script src="<?php echo get_asset_with_integrity(ASSETS_URL . '/js/main.js'); ?>"></script>

    <!-- Fallback for script loading errors -->
    <script>
        // Check if main.js failed to load
        window.addEventListener('error', function(e) {
            const target = e.target;
            // Only handle script loading errors
            if (target.tagName === 'SCRIPT' && target.src.includes('main.js')) {
                console.warn('Main script failed to load. Attempting to load fallback...');
                // You could load a minimal fallback script here
                const fallback = document.createElement('script');
                fallback.innerHTML = 'console.log("Using fallback basic functionality");';
                document.body.appendChild(fallback);
            }
        }, true); // Use capture to catch the error during loading
    </script>
    </body>

    </html>
    <?php
    // Хэрэв output буфер идэвхтэй бол флаш хийх
    if (ob_get_level() > 0) ob_end_flush();
    ?>