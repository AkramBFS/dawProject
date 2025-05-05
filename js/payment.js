document.addEventListener('DOMContentLoaded', function() {
    // DOM Elements
    const paymentForm = document.getElementById('payment-form');
    const paymentDetails = document.getElementById('payment-details');
    const paymentMethod = document.getElementById('payment-method');
    const creditCardDetails = document.getElementById('credit-card-details');
    
    // Credit Card Fields (moved outside the submit handler)
    const cardNumberEl = document.getElementById('card-number');
    const expiryDateEl = document.getElementById('expiry-date');
    const cvvEl = document.getElementById('cvv');

    // First verify all required elements exist
    if (!paymentForm || !paymentDetails || !paymentMethod || !creditCardDetails) {
        console.error('Critical payment form elements missing!');
        return;
    }

    // Toggle credit card fields based on payment method
    paymentMethod.addEventListener('change', function() {
        if (creditCardDetails) {
            creditCardDetails.style.display = this.value === 'credit-card' ? 'block' : 'none';
        }
    });

    // Load and display cart items
    function loadCartItems() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        if (cart.length === 0) {
            paymentDetails.innerHTML = '<p>Your cart is empty. <a href="index.html">Continue shopping</a></p>';
            paymentForm.style.display = 'none';
            return;
        }

        let total = 0;
        let itemsHtml = '<h2>Order Summary</h2><ul class="order-items">';
        
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;
            
            itemsHtml += `
                <li>
                    <span class="item-name">${item.name} (${item.size || 'One Size'})</span>
                    <span class="item-quantity">× ${item.quantity}</span>
                    <span class="item-price">$${itemTotal.toFixed(2)}</span>
                </li>
            `;
        });

        itemsHtml += `</ul>
            <div class="order-total">
                <span>Total:</span>
                <span>$${total.toFixed(2)}</span>
            </div>
            <input type="hidden" id="total-amount" value="${total.toFixed(2)}">`;

        paymentDetails.innerHTML = itemsHtml;
    }

    // Form submission handler
    paymentForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        if (cart.length === 0) {
            alert('Your cart is empty');
            return;
        }

        const totalAmountElement = document.getElementById('total-amount');
        if (!totalAmountElement) {
            alert('Error: Could not determine order total');
            return;
        }

        const formData = {
            shipping_address: document.getElementById('address').value.trim(),
            payment_method: paymentMethod.value,
            total: parseFloat(totalAmountElement.value),
            items: cart.map(item => ({
                product_variant_id: item.variant_id || item.product_variant_id,
                quantity: item.quantity,
                price: item.price
            }))
        };

        // Basic validation
        if (!formData.shipping_address) {
            alert('Please enter a shipping address');
            return;
        }

        // Validate that all items have a product_variant_id
        const missingVariantId = formData.items.some(item => !item.product_variant_id);
        if (missingVariantId) {
            alert('Error: Some items in your cart are invalid. Please clear your cart and try again.');
            return;
        }

        // Only validate credit card fields if credit card is selected
        if (paymentMethod.value === 'credit-card') {
            // Verify credit card fields exist
            if (!cardNumberEl || !expiryDateEl || !cvvEl) {
                console.error('Missing credit card fields:', {
                    cardNumberEl, 
                    expiryDateEl, 
                    cvvEl
                });
                alert('Payment system error. Please try again later.');
                return;
            }

            const cardNumber = cardNumberEl.value.trim();
            const expiryDate = expiryDateEl.value.trim();
            const cvv = cvvEl.value.trim();
            
            // Basic presence check
            if (!cardNumber || !expiryDate || !cvv) {
                alert('Please enter all credit card details');
                return;
            }

            // Validate card number format (simple 16-digit check)
            if (!/^\d{16}$/.test(cardNumber.replace(/\s/g, ''))) {
                alert('Please enter a valid 16-digit card number');
                return;
            }

            // Validate expiry date format (MM/YY)
            if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
                alert('Please enter a valid expiry date (MM/YY)');
                return;
            }

            // Validate CVV format (3 or 4 digits)
            if (!/^\d{3,4}$/.test(cvv)) {
                alert('Please enter a valid CVV (3 or 4 digits)');
                return;
            }
        }

        try {
            const response = await fetch('../php/process_payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                // Clear cart on success
                localStorage.removeItem('cart');
                // Redirect to confirmation page
                window.location.href = `order_confirmation.html?order_id=${result.order_id}`;
            } else {
                alert(`Payment failed: ${result.error || 'Unknown error'}`);
            }
        } catch (error) {
            console.error('Payment processing error:', error);
            alert('An error occurred while processing your payment. Please try again.');
        }
    });

    // Initialize the page
    loadCartItems();
});

function cancelPayment() {
    window.location.href = 'cart.html';
}