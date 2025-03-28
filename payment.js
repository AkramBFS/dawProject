// Retrieve cart from localStorage
const cart = JSON.parse(localStorage.getItem("cart")) || [];
const paymentDetails = document.getElementById("payment-details");

if (cart.length > 0) {
    let totalPrice = 0;

    // Calculate total price
    cart.forEach(product => {
        totalPrice += product.price * (product.quantity || 1);
    });

    // Display payment details
    paymentDetails.innerHTML = `
        <p>Total Amount: $${totalPrice.toFixed(2)}</p>
    `;
} else {
    // If cart is empty, show a message
    paymentDetails.innerHTML = `<p>Your cart is empty. <a href="index.html">Continue Shopping</a></p>`;
}

// Handle payment form submission
document.getElementById("payment-form").addEventListener("submit", function (event) {
    event.preventDefault();

    const paymentMethod = document.getElementById("payment-method").value;

    // Simulate payment processing
    alert(`Payment of $${totalPrice.toFixed(2)} via ${paymentMethod} has been confirmed.`);
    localStorage.removeItem("cart"); // Clear the cart after payment
    window.location.href = "index.html"; // Redirect to home page
});

// Cancel payment
function cancelPayment() {
    if (confirm("Are you sure you want to cancel the payment?")) {
        window.location.href = "cart.html"; // Redirect back to cart
    }
}
document.addEventListener("DOMContentLoaded", () => {
    const paymentForm = document.getElementById("payment-form");
    const paymentMethod = document.getElementById("payment-method");
    const creditCardDetails = document.getElementById("credit-card-details");

    // Show/hide credit card details based on payment method
    paymentMethod.addEventListener("change", () => {
        if (paymentMethod.value === "credit-card") {
            creditCardDetails.style.display = "block";
        } else {
            creditCardDetails.style.display = "none";
        }
    });

    // Form submission handler
    paymentForm.addEventListener("submit", (event) => {
        event.preventDefault(); // Prevent form submission

        // Get form values
        const name = document.getElementById("name").value.trim();
        const address = document.getElementById("address").value.trim();
        const selectedPaymentMethod = paymentMethod.value;
        const cardNumber = document.getElementById("card-number")?.value.trim();
        const expiryDate = document.getElementById("expiry-date")?.value.trim();
        const cvv = document.getElementById("cvv")?.value.trim();

        // Validate inputs
        if (!name || !address) {
            alert("Please fill out all required fields.");
            return;
        }

        if (selectedPaymentMethod === "credit-card") {
            if (!cardNumber || !expiryDate || !cvv) {
                alert("Please fill out all credit card details.");
                return;
            }

            // Validate card number (simple check for 16 digits)
            if (!/^\d{16}$/.test(cardNumber.replace(/\s/g, ""))) {
                alert("Please enter a valid 16-digit card number.");
                return;
            }

            // Validate expiry date (MM/YY format)
            if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
                alert("Please enter a valid expiry date (MM/YY).");
                return;
            }

            // Validate CVV (3 or 4 digits)
            if (!/^\d{3,4}$/.test(cvv)) {
                alert("Please enter a valid CVV (3 or 4 digits).");
                return;
            }
        }

        // If all validations pass, proceed with payment
        alert("Payment successful! Thank you for your purchase.");
        // return to homepage rak fahm
        window.location.href = "index.html";
    });
});

// Cancel payment handler
function cancelPayment() {
    if (confirm("Are you sure you want to cancel the payment?")) {
        window.location.href = "cart.html"; // Redirect back to the cart page
    }
}