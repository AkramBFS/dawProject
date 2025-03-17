// Retrieve cart from localStorage
let cart = JSON.parse(localStorage.getItem("cart")) || [];
const cartItems = document.getElementById("cart-items");
const cartTotal = document.getElementById("cart-total");

// Function to display cart items
function displayCart() {
    cartItems.innerHTML = ""; // Clear the cart display
    let totalPrice = 0;

    if (cart.length > 0) {
        cart.forEach((product, index) => {
            const itemDiv = document.createElement("div");
            itemDiv.className = "cart-item";
            itemDiv.innerHTML = `
                <img src="${product.image}" alt="${product.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <h2 class="cart-item-name">${product.name}</h2>
                    <p class="cart-item-price">Price: $${product.price.toFixed(2)}</p>
                    <p class="cart-item-quantity">Quantity: ${product.quantity || 1}</p>
                    <button onclick="removeFromCart(${index})" class="remove-item-btn">Remove</button>
                </div>
            `;
            cartItems.appendChild(itemDiv);

            // Calculate total price
            totalPrice += product.price * (product.quantity || 1);
        });

        // Display total price
        cartTotal.innerHTML = `<p>Total: $${totalPrice.toFixed(2)}</p>`;
    } else {
        // If cart is empty, show a message
        cartItems.innerHTML = `<p>Your cart is empty.</p>`;
        cartTotal.innerHTML = `<p>Total: $0.00</p>`;
    }
}

// Function to remove an item from the cart
function removeFromCart(index) {
    cart.splice(index, 1); // Remove the item at the specified index
    localStorage.setItem("cart", JSON.stringify(cart)); // Update localStorage
    displayCart(); // Refresh the cart display
}

// Display the cart when the page loads
displayCart();