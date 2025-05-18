document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('authModal');
    const modalContent = document.getElementById('modalContent');
    const modalBackdrop = document.getElementById('modalBackdrop');
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const closeModal = document.getElementById('closeModal');

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
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
    }

    // Switch to register tab
    function switchToRegister() {
        registerTab.classList.remove('text-gray-500');
        loginTab.classList.add('text-gray-500');
        registerTab.querySelector('div').classList.remove('translate-x-full');
        loginTab.querySelector('div').classList.add('translate-x-full');
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
    }

    // Event listeners
    loginTab.addEventListener('click', switchToLogin);
    registerTab.addEventListener('click', switchToRegister);
    closeModal.addEventListener('click', hideModal);
    modalBackdrop.addEventListener('click', hideModal);

    // Handle form submissions
    loginForm.querySelector('form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        try {
            const response = await fetch('api/auth/login.php', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showNotification(data.message || 'Login successful!', 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Login failed', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    registerForm.querySelector('form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        try {
            const response = await fetch('api/auth/register.php', {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData)),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                showNotification(data.message || 'Registration successful!', 'success');
                setTimeout(() => {
                    switchToLogin();
                }, 1000);
            } else {
                showNotification(data.message || 'Registration failed', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            showNotification('An error occurred', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Add keyboard support
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
