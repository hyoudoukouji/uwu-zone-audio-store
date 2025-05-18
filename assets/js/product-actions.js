document.addEventListener('DOMContentLoaded', function() {
    // Add to Cart functionality
    const addToCartButtons = document.querySelectorAll('[data-action="add-to-cart"]');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            const quantity = document.getElementById('quantity')?.value || 1;
            addToCart(productId, quantity);
        });
    });

    // Toggle Wishlist functionality
    const wishlistButtons = document.querySelectorAll('[data-action="toggle-wishlist"]');
    wishlistButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            toggleWishlist(productId, this);
        });
    });

    function addToCart(productId, quantity) {
        fetch('/api/cart/add.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                productId: productId,
                quantity: parseInt(quantity)
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showNotification('Product added to cart successfully', 'success');
                // Update cart count if it exists
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.cartCount;
                    cartCount.classList.remove('hidden');
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to add product to cart', 'error');
        });
    }

    function toggleWishlist(productId, button) {
        fetch('/api/wishlist/toggle.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                productId: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Toggle heart icon color
                const heartIcon = button.querySelector('i.fa-heart');
                if (data.action === 'added') {
                    heartIcon.classList.remove('text-blue-600');
                    heartIcon.classList.add('text-red-500');
                    showNotification('Product added to wishlist', 'success');
                } else {
                    heartIcon.classList.remove('text-red-500');
                    heartIcon.classList.add('text-blue-600');
                    showNotification('Product removed from wishlist', 'success');
                }

                // Update wishlist count if it exists
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    if (data.wishlistCount > 0) {
                        wishlistCount.textContent = data.wishlistCount;
                        wishlistCount.classList.remove('hidden');
                    } else {
                        wishlistCount.classList.add('hidden');
                    }
                }
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Failed to update wishlist', 'error');
        });
    }

    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white z-50`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
