// Google Sign-In configuration
window.onload = function() {
    google.accounts.id.initialize({
        client_id: '847551439598-lpf7vog13e8qgk8mb0rmd80l4v1mhnst.apps.googleusercontent.com',
        callback: handleGoogleResponse,
        auto_select: false,
        allowed_parent_origin: 'https://z6yw42-8000.csb.app'
    });

    // Render the Google Sign-In button for both login and register forms
    google.accounts.id.renderButton(
        document.getElementById('googleLoginButton'),
        { 
            type: 'standard',
            theme: 'outline',
            size: 'large',
            text: 'continue_with',
            shape: 'rectangular',
            width: 300,
            logo_alignment: 'left'
        }
    );
};

function handleGoogleResponse(response) {
    if (response.credential) {
        // Show loading state
        const activeForm = document.getElementById('registerForm').style.display !== 'none' ? 'register' : 'login';
        const button = document.querySelector(`#${activeForm}Form button[type="submit"]`);
        const buttonText = button.querySelector('.button-text');
        const loadingText = button.querySelector('.loading-text');
        
        buttonText.classList.add('hidden');
        loadingText.classList.remove('hidden');
        button.disabled = true;

        // Send the ID token to your server
        fetch('/api/auth/google_auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                credential: response.credential,
                isRegister: activeForm === 'register'
            }),
            credentials: 'include' // Important: This ensures cookies/session are sent
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message || 'Successfully authenticated with Google!', 'success');
                // Store user data if provided
                if (data.user) {
                    localStorage.setItem('user', JSON.stringify(data.user));
                }
                // Update auth state and reload page after a short delay
                window.isUserLoggedIn = true;
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Authentication failed', 'error');
                // Reset button state
                buttonText.classList.remove('hidden');
                loadingText.classList.add('hidden');
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Authentication Error:', error);
            showNotification('Authentication failed. Please try again.', 'error');
            // Reset button state
            buttonText.classList.remove('hidden');
            loadingText.classList.add('hidden');
            button.disabled = false;
        });
    }
}

// Helper function to show notifications
function showNotification(message, type = 'success') {
    const notification = document.getElementById('notification');
    const messageEl = notification.querySelector('.notification-message');
    const iconEl = notification.querySelector('.notification-icon');
    
    messageEl.textContent = message;
    
    if (type === 'success') {
        notification.className = 'fixed bottom-4 right-4 transform transition-all duration-300 bg-green-50 text-green-800 p-4 rounded-lg shadow-lg flex items-center space-x-3';
        iconEl.className = 'fas fa-check-circle text-2xl text-green-500 notification-icon';
    } else {
        notification.className = 'fixed bottom-4 right-4 transform transition-all duration-300 bg-red-50 text-red-800 p-4 rounded-lg shadow-lg flex items-center space-x-3';
        iconEl.className = 'fas fa-exclamation-circle text-2xl text-red-500 notification-icon';
    }
    
    notification.classList.remove('translate-y-full', 'opacity-0');
    
    setTimeout(() => {
        notification.classList.add('translate-y-full', 'opacity-0');
    }, 3000);
}

// Hide notification function
function hideNotification() {
    const notification = document.getElementById('notification');
    notification.classList.add('translate-y-full', 'opacity-0');
}
