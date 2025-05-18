document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const modal = document.getElementById('authModal');
    const modalContent = document.getElementById('modalContent');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginFormElement');
    const registerForm = document.getElementById('registerFormElement');
    const closeModal = document.getElementById('closeModal');
    const notification = document.getElementById('notification');

    // Show modal function
    window.showAuthModal = function(mode = 'login') {
        modal.classList.remove('hidden');
        setTimeout(() => {
            modalBackdrop.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95', 'opacity-0');
        }, 10);
        
        if (mode === 'register') {
            switchToRegister();
        } else {
            switchToLogin();
        }
    };

    // Hide modal function
    function hideModal() {
        modalBackdrop.classList.add('opacity-0');
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    // Switch to login tab
    function switchToLogin() {
        loginTab.classList.remove('text-gray-500');
        registerTab.classList.add('text-gray-500');
        loginTab.querySelector('div').classList.remove('translate-x-full');
        registerTab.querySelector('div').classList.add('translate-x-full');
        loginTab.setAttribute('aria-selected', 'true');
        registerTab.setAttribute('aria-selected', 'false');
        document.getElementById('loginForm').classList.remove('hidden');
        document.getElementById('registerForm').classList.add('hidden');
    }

    // Switch to register tab
    function switchToRegister() {
        registerTab.classList.remove('text-gray-500');
        loginTab.classList.add('text-gray-500');
        registerTab.querySelector('div').classList.remove('translate-x-full');
        loginTab.querySelector('div').classList.add('translate-x-full');
        registerTab.setAttribute('aria-selected', 'true');
        loginTab.setAttribute('aria-selected', 'false');
        document.getElementById('registerForm').classList.remove('hidden');
        document.getElementById('loginForm').classList.add('hidden');
    }

    // Show notification
    window.showNotification = function(message, type = 'success') {
        const notificationEl = document.getElementById('notification');
        const iconEl = notificationEl.querySelector('.notification-icon');
        const messageEl = notificationEl.querySelector('.notification-message');
        
        // Reset any existing animations
        notificationEl.style.animation = 'none';
        notificationEl.offsetHeight; // Trigger reflow
        notificationEl.style.animation = null;
        
        // Set icon and colors based on type
        if (type === 'success') {
            notificationEl.className = 'fixed bottom-4 right-4 transform transition-all duration-300 bg-green-50 text-green-800 p-4 rounded-lg shadow-lg flex items-center space-x-3';
            iconEl.className = 'fas fa-check-circle text-2xl text-green-500 notification-icon';
        } else {
            notificationEl.className = 'fixed bottom-4 right-4 transform transition-all duration-300 bg-red-50 text-red-800 p-4 rounded-lg shadow-lg flex items-center space-x-3';
            iconEl.className = 'fas fa-exclamation-circle text-2xl text-red-500 notification-icon';
        }
        
        messageEl.textContent = message;
        
        // Show notification with slide-up animation
        notificationEl.classList.remove('translate-y-full', 'opacity-0');
        notificationEl.style.animation = 'slideUp 0.5s ease forwards';
        
        // Hide after 3 seconds
        setTimeout(() => {
            notificationEl.style.animation = 'slideDown 0.5s ease forwards';
            setTimeout(() => {
                notificationEl.classList.add('translate-y-full', 'opacity-0');
            }, 450);
        }, 3000);
    };

    // Hide notification
    window.hideNotification = function() {
        const notificationEl = document.getElementById('notification');
        notificationEl.classList.add('translate-y-full', 'opacity-0');
    };

    // Form validation and submission
    function validateForm(form) {
        const errors = {};
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        // Validate email
        const emailInput = form.querySelector('input[name="email"]');
        if (!emailInput || !emailInput.value) {
            errors.email = 'Email is required';
        } else if (!emailRegex.test(emailInput.value)) {
            errors.email = 'Please enter a valid email address';
        }
        
        // Validate password
        const passwordInput = form.querySelector('input[name="password"]');
        if (!passwordInput || !passwordInput.value) {
            errors.password = 'Password is required';
        } else if (passwordInput.value.length < 6) {
            errors.password = 'Password must be at least 6 characters';
        }
        
        // Additional validation for registration
        if (form.id === 'registerFormElement') {
            const usernameInput = form.querySelector('input[name="username"]');
            if (!usernameInput.value) {
                errors.username = 'Username is required';
            } else if (usernameInput.value.length < 3) {
                errors.username = 'Username must be at least 3 characters';
            }
            
            const confirmPasswordInput = form.querySelector('input[name="confirm_password"]');
            if (!confirmPasswordInput.value) {
                errors.confirmPassword = 'Please confirm your password';
            } else if (confirmPasswordInput.value !== passwordInput.value) {
                errors.confirmPassword = 'Passwords do not match';
            }
        }
        
        return errors;
    }

    // Display form errors
    function displayErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
        
        // Display new errors
        Object.keys(errors).forEach(field => {
            const errorDiv = document.getElementById(`${field}-error`);
            if (errorDiv) {
                errorDiv.textContent = errors[field];
                errorDiv.classList.remove('hidden');
            }
        });
    }

    // Update UI after successful login
    function updateUIAfterLogin(userData) {
        // Update cart count
        const cartCountElement = document.getElementById('cart-count');
        if (cartCountElement && userData.cartCount > 0) {
            cartCountElement.textContent = userData.cartCount;
            cartCountElement.classList.remove('hidden');
        }

        // Update wishlist count if exists
        const wishlistCountElement = document.getElementById('wishlist-count');
        if (wishlistCountElement && userData.wishlistCount > 0) {
            wishlistCountElement.textContent = userData.wishlistCount;
            wishlistCountElement.classList.remove('hidden');
        }

        // Update user profile section
        const userProfileSection = document.getElementById('user-profile-section');
        const loginButton = document.getElementById('login-button');
        
        if (userProfileSection && loginButton) {
            userProfileSection.classList.remove('hidden');
            loginButton.classList.add('hidden');
        }

        // Set global login state
        window.isUserLoggedIn = true;

        // Hide modal
        hideModal();
    }

    // Handle form submissions
    function handleSubmit(e, formType) {
        e.preventDefault();
        const form = e.target;
        const errors = validateForm(form);
        
        if (Object.keys(errors).length > 0) {
            displayErrors(form, errors);
            return;
        }
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const buttonText = submitBtn.querySelector('.button-text');
        const loadingText = submitBtn.querySelector('.loading-text');
        
        // Show loading state
        submitBtn.disabled = true;
        buttonText.classList.add('hidden');
        loadingText.classList.remove('hidden');
        
        // Clear previous errors
        form.querySelectorAll('.text-red-500').forEach(el => el.classList.add('hidden'));
        
        // Prepare form data
        const formData = new FormData(form);
        
        // Prepare form data as an object
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        // Log form data object
        console.log('Form data object:', formDataObj);

        // Send request
        fetch(`api/auth/${formType}.php`, {
            method: 'POST',
            body: JSON.stringify(formDataObj),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification(data.message, 'success');
                if (formType === 'login' || (formType === 'register' && data.user)) {
                    updateUIAfterLogin(data);
                    // Immediately reload page to sync session state
                    window.location.reload();
                } else if (formType === 'register') {
                    setTimeout(() => {
                        switchToLogin();
                    }, 1000);
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('An error occurred. Please try again.', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            // Reset button state
            submitBtn.disabled = false;
            buttonText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        });
    }

    // Event listeners
    loginTab.addEventListener('click', switchToLogin);
    registerTab.addEventListener('click', switchToRegister);
    closeModal.addEventListener('click', hideModal);
    modalBackdrop.addEventListener('click', hideModal);
    
    loginForm.addEventListener('submit', e => handleSubmit(e, 'login'));
    registerForm.addEventListener('submit', e => handleSubmit(e, 'register'));

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            hideModal();
        }
    });

    // Prevent form submission when pressing Enter in input fields
    modal.querySelectorAll('input').forEach(input => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const form = this.closest('form');
                const submitBtn = form.querySelector('button[type="submit"]');
                submitBtn.click();
            }
        });
    });
});
