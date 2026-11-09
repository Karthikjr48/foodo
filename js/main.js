// =========================================================
// MAIN.JS - Shared site behavior (used on every page)
// Premium animations + all existing functionality
// =========================================================

document.addEventListener('DOMContentLoaded', () => {

    // =====================================================
    // 1. MOBILE NAVBAR TOGGLE (Enhanced)
    // =====================================================
    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('open');
            navLinks.classList.toggle('active');
            hamburger.classList.toggle('active');

            // Toggle between bars and X icon
            const icon = hamburger.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-bars');
                icon.classList.toggle('fa-times');
            }

            // Prevent body scroll when menu is open
            document.body.classList.toggle('menu-open');
        });

        // Close menu when a nav link is clicked
        navLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navLinks.classList.remove('open', 'active');
                hamburger.classList.remove('active');
                document.body.classList.remove('menu-open');

                const icon = hamburger.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        });
    }

    // =====================================================
    // 2. NAVBAR SCROLL EFFECT
    // =====================================================
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // =====================================================
    // 3. SCROLL ANIMATIONS (IntersectionObserver)
    // =====================================================
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.fade-in-up, .fade-in-left, .fade-in-right, .scale-in').forEach(el => {
        observer.observe(el);
    });

    // =====================================================
    // 4. SMOOTH COUNTER ANIMATION FOR HERO STATS
    // =====================================================
    function animateCounter(el, target, suffix = '') {
        let current = 0;
        const increment = target / 60;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            el.textContent = Math.floor(current) + suffix;
        }, 16);
    }

    // Observe stat elements and animate when they scroll into view
    const statElements = document.querySelectorAll('[data-count]');
    if (statElements.length > 0) {
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.dataset.count, 10);
                    const suffix = entry.target.dataset.suffix || '';
                    animateCounter(entry.target, target, suffix);
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        statElements.forEach(el => statsObserver.observe(el));
    }

    // =====================================================
    // 5. REVIEWS CAROUSEL AUTO-SCROLL
    // =====================================================
    const reviewsGrid = document.querySelector('.reviews-grid');
    if (reviewsGrid) {
        let scrollAmount = 0;
        const scrollStep = 1;
        let autoScrollInterval;

        function startAutoScroll() {
            autoScrollInterval = setInterval(() => {
                scrollAmount += scrollStep;
                if (scrollAmount >= reviewsGrid.scrollWidth - reviewsGrid.clientWidth) {
                    scrollAmount = 0;
                }
                reviewsGrid.scrollTo({ left: scrollAmount, behavior: 'smooth' });
            }, 30);
        }

        // Pause on hover
        reviewsGrid.addEventListener('mouseenter', () => clearInterval(autoScrollInterval));
        reviewsGrid.addEventListener('mouseleave', startAutoScroll);
        startAutoScroll();
    }

    // =====================================================
    // 6. FOOD CARD 3D TILT EFFECT
    // =====================================================
    document.querySelectorAll('.food-card').forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
            card.style.transition = 'transform 0.4s ease';
        });
    });

    // =====================================================
    // 7. RIPPLE EFFECT ON BUTTONS
    // =====================================================
    document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            ripple.style.cssText = `
                position:absolute; border-radius:50%; background:rgba(255,255,255,0.3);
                width:100px; height:100px; transform:translate(-50%,-50%) scale(0);
                animation: ripple 0.6s ease-out; pointer-events:none;
                left:${e.clientX - rect.left}px; top:${e.clientY - rect.top}px;
            `;
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // =====================================================
    // 8. TYPED TEXT EFFECT FOR HERO EYEBROW
    // =====================================================
    const eyebrow = document.querySelector('.hero .eyebrow');
    if (eyebrow) {
        const texts = [
            '🔥 Hungry? We\'ve got you.',
            '🍕 500+ Dishes Available',
            '⚡ 25 Min Delivery',
            '⭐ 4.8 Star Rating'
        ];
        let textIndex = 0;
        setInterval(() => {
            textIndex = (textIndex + 1) % texts.length;
            eyebrow.style.opacity = '0';
            eyebrow.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                eyebrow.textContent = texts[textIndex];
                eyebrow.style.opacity = '1';
                eyebrow.style.transform = 'translateY(0)';
            }, 300);
        }, 3000);
    }

    // =====================================================
    // 9. MENU PAGE: CATEGORY FILTER + SEARCH
    // =====================================================
    const filterBar = document.getElementById('filterBar');
    const foodGrid = document.getElementById('foodGrid');
    const menuSearch = document.getElementById('menuSearch');
    const noResults = document.getElementById('noResults');

    if (filterBar && foodGrid) {
        const filterChips = filterBar.querySelectorAll('.filter-chip');
        const foodCards = foodGrid.querySelectorAll('.food-card');

        function applyFilters() {
            const activeChip = filterBar.querySelector('.filter-chip.active');
            const activeFilter = activeChip ? activeChip.dataset.filter : 'all';
            const searchTerm = menuSearch ? menuSearch.value.trim().toLowerCase() : '';
            let visibleCount = 0;

            foodCards.forEach(card => {
                const matchesCategory = activeFilter === 'all' || card.dataset.category === activeFilter;
                const matchesSearch = searchTerm === '' || card.dataset.name.includes(searchTerm);

                if (matchesCategory && matchesSearch) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        // Category chip clicks
        filterChips.forEach(chip => {
            chip.addEventListener('click', () => {
                filterChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');
                applyFilters();
            });
        });

        // ---- Auto-apply filter if the page was opened with ?category=slug in the URL ----
        // (used by the Home page's category circles, e.g. menu.php?category=pizza)
        const urlParams = new URLSearchParams(window.location.search);
        const categoryFromUrl = urlParams.get('category');
        if (categoryFromUrl) {
            const matchingChip = filterBar.querySelector(`.filter-chip[data-filter="${categoryFromUrl}"]`);
            if (matchingChip) {
                matchingChip.click();
                matchingChip.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
            }
        }

        // Live search as the user types (prevents page reload)
        if (menuSearch) {
            menuSearch.closest('form').addEventListener('submit', (e) => {
                e.preventDefault();
                applyFilters();
            });
            menuSearch.addEventListener('input', applyFilters);
        }
    }

    // =====================================================
    // 10. CART PAGE: QUANTITY, REMOVE, LIVE TOTALS
    // =====================================================
    const cartItemsWrap = document.getElementById('cartItems');
    const cartSummary = document.getElementById('cartSummary');
    const emptyCart = document.getElementById('emptyCart');
    const cartCountBadge = document.getElementById('cartCount');

    const DELIVERY_FEE = 40;
    const TAX_RATE = 0.05;

    function formatRupee(amount) {
        return '₹' + Math.round(amount);
    }

    function recalculateCart() {
        if (!cartItemsWrap) return;

        const items = cartItemsWrap.querySelectorAll('.cart-item');
        let subtotal = 0;
        let totalQty = 0;

        items.forEach(item => {
            const price = parseFloat(item.dataset.price);
            const qty = parseInt(item.dataset.qty, 10);
            const lineTotal = price * qty;
            subtotal += lineTotal;
            totalQty += qty;
            item.querySelector('.cart-item-subtotal').textContent = formatRupee(lineTotal);
        });

        const tax = subtotal * TAX_RATE;
        const delivery = items.length > 0 ? DELIVERY_FEE : 0;
        const grandTotal = subtotal + tax + delivery;

        const subtotalEl = document.getElementById('summarySubtotal');
        const deliveryEl = document.getElementById('summaryDelivery');
        const taxEl = document.getElementById('summaryTax');
        const totalEl = document.getElementById('summaryTotal');

        if (subtotalEl) subtotalEl.textContent = formatRupee(subtotal);
        if (deliveryEl) deliveryEl.textContent = formatRupee(delivery);
        if (taxEl) taxEl.textContent = formatRupee(tax);
        if (totalEl) totalEl.textContent = formatRupee(grandTotal);
        if (cartCountBadge) cartCountBadge.textContent = totalQty;

        // Toggle empty-cart state
        if (emptyCart && cartSummary) {
            if (items.length === 0) {
                cartItemsWrap.style.display = 'none';
                cartSummary.style.display = 'none';
                emptyCart.style.display = 'block';
            } else {
                cartItemsWrap.style.display = 'flex';
                cartSummary.style.display = 'block';
                emptyCart.style.display = 'none';
            }
        }
    }

    if (cartItemsWrap) {
        cartItemsWrap.addEventListener('click', (e) => {
            const item = e.target.closest('.cart-item');
            if (!item) return;

            // Increase quantity
            if (e.target.closest('.qty-increase')) {
                const newQty = parseInt(item.dataset.qty, 10) + 1;
                item.dataset.qty = newQty;
                item.querySelector('.qty-value').textContent = newQty;
                recalculateCart();
            }

            // Decrease quantity (minimum 1)
            if (e.target.closest('.qty-decrease')) {
                const newQty = Math.max(1, parseInt(item.dataset.qty, 10) - 1);
                item.dataset.qty = newQty;
                item.querySelector('.qty-value').textContent = newQty;
                recalculateCart();
            }

            // Remove item
            if (e.target.closest('.remove-item')) {
                item.remove();
                recalculateCart();
            }
        });

        // Run once on load to sync totals with initial data
        recalculateCart();
    }

    // =====================================================
    // 11. AUTH PAGES: SHOW/HIDE PASSWORD
    // =====================================================
    const togglePasswordButtons = document.querySelectorAll('.toggle-password');

    togglePasswordButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.previousElementSibling;
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    });

    // =====================================================
    // 12. REGISTER PAGE: PASSWORD MATCH VALIDATION
    // =====================================================
    const registerForm = document.getElementById('registerForm');

    if (registerForm) {
        const password = document.getElementById('regPassword');
        const confirmPassword = document.getElementById('regConfirmPassword');
        const passwordError = document.getElementById('passwordError');

        registerForm.addEventListener('submit', (e) => {
            if (password.value !== confirmPassword.value) {
                e.preventDefault();
                passwordError.classList.add('show');
                confirmPassword.focus();
            } else {
                passwordError.classList.remove('show');
            }
        });

        confirmPassword.addEventListener('input', () => {
            passwordError.classList.remove('show');
        });
    }

    // =====================================================
    // 13. FORGOT PASSWORD PAGE: REQUEST -> CONFIRMATION STEP
    // =====================================================
    const forgotForm = document.getElementById('forgotForm');

    if (forgotForm) {
        const requestStep = document.getElementById('requestStep');
        const confirmStep = document.getElementById('confirmStep');
        const sentEmail = document.getElementById('sentEmail');
        const resendLink = document.getElementById('resendLink');

        forgotForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('forgotEmail').value;
            sentEmail.textContent = email;
            requestStep.style.display = 'none';
            confirmStep.style.display = 'block';
        });

        if (resendLink) {
            resendLink.addEventListener('click', (e) => {
                e.preventDefault();
                alert('A new reset link has been sent to ' + sentEmail.textContent);
            });
        }
    }

    // =====================================================
    // 14. PROFILE PAGE: SAVE CHANGES CONFIRMATION
    // =====================================================
    const profileForm = document.getElementById('profileForm');

    if (profileForm) {
        const saveConfirm = document.getElementById('saveConfirm');

        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            saveConfirm.classList.add('show');
            setTimeout(() => saveConfirm.classList.remove('show'), 3000);
        });
    }

    // =====================================================
    // 15. MY ORDERS PAGE: STATUS FILTER
    // =====================================================
    const ordersFilter = document.getElementById('ordersFilter');
    const ordersList = document.getElementById('ordersList');
    const ordersNoResults = document.getElementById('ordersNoResults');

    if (ordersFilter && ordersList) {
        const statusChips = ordersFilter.querySelectorAll('.filter-chip');
        const orderCards = ordersList.querySelectorAll('.order-card');

        statusChips.forEach(chip => {
            chip.addEventListener('click', () => {
                statusChips.forEach(c => c.classList.remove('active'));
                chip.classList.add('active');

                const status = chip.dataset.status;
                let visibleCount = 0;

                orderCards.forEach(card => {
                    if (status === 'all' || card.dataset.status === status) {
                        card.style.display = '';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (ordersNoResults) {
                    ordersNoResults.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        });
    }

    // =====================================================
    // 16. CONTACT PAGE: handled server-side (contact_process.php)
    // =====================================================

    // =====================================================
    // 17. AJAX ADD TO CART & FLY TO CART ANIMATION
    // =====================================================
    const quickAddBtns = document.querySelectorAll('.quick-add');
    if (quickAddBtns.length > 0) {
        quickAddBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const profileChip = document.querySelector('.profile-chip');
                
                // If user is not logged in, let the default redirection handle taking them to login page
                if (!profileChip) {
                    return;
                }
                
                // If logged in, intercept and send AJAX request
                e.preventDefault();
                
                const url = btn.getAttribute('href');
                const ajaxUrl = url + '&ajax=1';
                
                fetch(ajaxUrl)
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'success') {
                            animateFlyToCart(btn, data.cart_count);
                        }
                    })
                    .catch(err => {
                        console.error('AJAX cart error:', err);
                        window.location.href = url; // Fallback redirect on error
                    });
            });
        });
    }

    function animateFlyToCart(btn, newCount) {
        const card = btn.closest('.food-card');
        const img = card.querySelector('.food-img-wrap img');
        const cartIcon = document.querySelector('.cart-icon');
        
        if (!img || !cartIcon) return;
        
        // Clone food card image
        const clone = img.cloneNode(true);
        const imgRect = img.getBoundingClientRect();
        const cartRect = cartIcon.getBoundingClientRect();
        
        clone.style.position = 'fixed';
        clone.style.left = imgRect.left + 'px';
        clone.style.top = imgRect.top + 'px';
        clone.style.width = imgRect.width + 'px';
        clone.style.height = imgRect.height + 'px';
        clone.style.zIndex = '99999';
        clone.style.borderRadius = '12px';
        clone.style.boxShadow = '0 8px 20px rgba(0,0,0,0.3)';
        clone.style.transition = 'all 0.75s cubic-bezier(0.25, 1, 0.5, 1)';
        clone.style.pointerEvents = 'none';
        
        document.body.appendChild(clone);
        
        // Force reflow
        clone.offsetWidth;
        
        // Rotate and scale down to cart icon position
        clone.style.left = (cartRect.left + cartRect.width / 2 - 20) + 'px';
        clone.style.top = (cartRect.top + cartRect.height / 2 - 20) + 'px';
        clone.style.width = '24px';
        clone.style.height = '24px';
        clone.style.opacity = '0.3';
        clone.style.transform = 'rotate(360deg)';
        
        setTimeout(() => {
            clone.remove();
            
            // Pop the cart badge count
            const badge = document.querySelector('.cart-count');
            if (badge) {
                badge.textContent = newCount;
                badge.style.transform = 'scale(1.4)';
                badge.style.transition = 'transform 0.12s ease';
                setTimeout(() => {
                    badge.style.transform = 'scale(1)';
                }, 150);
            }
        }, 750);
    }

    // =====================================================
    // 18. WISHLIST / LIKED LIST SYSTEM (LocalStorage)
    // =====================================================
    const wishlistCountBadges = document.querySelectorAll('.wishlist-count');

    function getWishlist() {
        try {
            return JSON.parse(localStorage.getItem('foodo_wishlist')) || [];
        } catch (e) {
            return [];
        }
    }

    function saveWishlist(list) {
        localStorage.setItem('foodo_wishlist', JSON.stringify(list));
        updateWishlistBadge();
    }

    function updateWishlistBadge() {
        if (wishlistCountBadges.length === 0) return;
        const list = getWishlist();
        wishlistCountBadges.forEach(badge => {
            badge.textContent = list.length;
            if (list.length > 0) {
                badge.style.display = 'inline-flex';
            } else {
                badge.style.display = 'none';
            }
        });
    }

    // Initialize Favorites on all cards currently loaded on page
    const likeButtons = document.querySelectorAll('.like-btn');
    if (likeButtons.length > 0) {
        const currentList = getWishlist();
        likeButtons.forEach(btn => {
            const id = btn.dataset.id;
            const icon = btn.querySelector('i');
            if (currentList.includes(id)) {
                btn.classList.add('liked');
                if (icon) {
                    icon.classList.replace('fa-regular', 'fa-solid');
                }
            }
            
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation(); // Prevent food card click trigger
                
                let list = getWishlist();
                const icon = btn.querySelector('i');
                const idStr = id.toString();
                
                // Convert list items to string for solid comparison
                list = list.map(item => item.toString());
                
                if (list.includes(idStr)) {
                    // Remove from list
                    list = list.filter(item => item !== idStr);
                    btn.classList.remove('liked');
                    if (icon) {
                        icon.classList.replace('fa-solid', 'fa-regular');
                    }
                } else {
                    // Add to list
                    list.push(idStr);
                    btn.classList.add('liked');
                    if (icon) {
                        icon.classList.replace('fa-regular', 'fa-solid');
                    }
                }
                
                saveWishlist(list);
            });
        });
    }

    // Run once on load
    updateWishlistBadge();

}); // end DOMContentLoaded
