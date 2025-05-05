// Retrieve cart from localStorage
let cart = JSON.parse(localStorage.getItem("cart")) || [];
const cartItems = document.getElementById("cart-items");
const cartTotal = document.getElementById("cart-total");

function displayCart() {
    cartItems.innerHTML = "";
    let totalPrice = 0;

    if (cart.length > 0) {
        cart.forEach((item, index) => {
            const itemDiv = document.createElement("div");
            itemDiv.className = "cart-item";
            itemDiv.innerHTML = `
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <h2 class="cart-item-name">${item.name}</h2>
                    ${item.size ? `<p class="cart-item-size">Size: ${item.size}</p>` : ''}
                    <p class="cart-item-price">Price: $${item.price.toFixed(2)}</p>
                    <div class="quantity-controls">
                        <button onclick="updateQuantity(${index}, -1)">-</button>
                        <span class="cart-item-quantity">${item.quantity || 1}</span>
                        <button onclick="updateQuantity(${index}, 1)">+</button>
                    </div>
                    <button onclick="removeFromCart(${index})" class="remove-item-btn">Remove</button>
                </div>
            `;
            cartItems.appendChild(itemDiv);

            totalPrice += item.price * (item.quantity || 1);
        });

        cartTotal.innerHTML = `<p>Total: $${totalPrice.toFixed(2)}</p>`;
    } else {
        cartItems.innerHTML = `<p>Your cart is empty.</p>`;
        cartTotal.innerHTML = `<p>Total: $0.00</p>`;
    }
}

function updateQuantity(index, change) {
    cart[index].quantity = (cart[index].quantity || 1) + change;
    
    // Ensure quantity doesn't go below 1
    if (cart[index].quantity < 1) {
        cart[index].quantity = 1;
    }
    
    localStorage.setItem("cart", JSON.stringify(cart));
    displayCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart));
    displayCart();
}

displayCart();