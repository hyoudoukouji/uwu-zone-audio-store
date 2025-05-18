document.addEventListener('DOMContentLoaded', function() {
    // Initialize animations with delay
    const animatedElements = document.querySelectorAll('.animate-fade-in');
    animatedElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
    });

    // Handle quantity changes in product detail page
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            const max = parseInt(this.max);
            const min = parseInt(this.min);
            let value = parseInt(this.value);

            if (value > max) this.value = max;
            if (value < min) this.value = min;
            if (isNaN(value)) this.value = min;
        });
    }

    // Buy now functionality
    const buyNowButtons = document.querySelectorAll('[data-action="buy-now"]');
    buyNowButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (!window.isUserLoggedIn) {
                showNotification('You must be logged in to buy items', 'error');
                return;
            }
            const productId = this.getAttribute('data-product-id');
            buyNow(productId);
        });
    });

    function buyNow(productId) {
        const quantity = 1;
        // Show loading state on Buy Now button
        const button = document.querySelector(`[data-action="buy-now"][data-product-id="${productId}"]`);
        let originalText = '';
        if (button) {
            originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            button.disabled = true;
        }

        fetch('api/cart/add.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ productId: productId, quantity: quantity })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('Product added to cart. Redirecting to checkout...', 'success');
                setTimeout(() => {
                    window.location.href = `checkout.php?product=${productId}&quantity=${quantity}`;
                }, 1000);
            } else {
                showNotification(data.message || 'Failed to add product', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding product to cart', 'error');
        })
        .finally(() => {
            if (button) {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }

    // Utility functions
    function showNotification(message, type = 'success') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => {
            notification.remove();
        });

        // Create new notification
        const notification = document.createElement('div');
        notification.className = `notification fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white transform transition-all duration-300 translate-y-0 opacity-100 flex items-center z-50`;
        
        const icon = document.createElement('i');
        icon.className = `fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2`;
        notification.appendChild(icon);
        
        const text = document.createElement('span');
        text.textContent = message;
        notification.appendChild(text);

        document.body.appendChild(notification);

        // Animate out and remove
        setTimeout(() => {
            notification.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 2000);
    }
});
