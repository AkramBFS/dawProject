// Product data
const products = [
    {
        id: 1,
        name: "T-Shirts for Men (S)",
        price: 41.99,
        image: "image/prods/T-Shirts for Men.jpg",
        category: "T-shirts",
        size: "S",
        rating: 3,
        description: "Comfortable and stylish T-shirts for men."
    },
    {
        id: 2,
        name: "T-Shirts for Men (L)",
        price: 41.99,
        image: "image/prods/T-Shirts for Men.jpg",
        category: "T-shirts",
        size: "L",
        rating: 3,
        description: "Comfortable and stylish T-shirts for men."
    },
    {
        id: 3,
        name: "Sports Shoes (39)",
        price: 139.99,
        image: "image/prods/Sports Shoes.webp",
        category: "Shoes",
        size: "39",
        rating: 4.5,
        description: "High-performance sports shoes for all activities."
    },
    {
        id: 4,
        name: "Sports Shoes (40)",
        price: 139.99,
        image: "image/prods/Sports Shoes.webp",
        category: "Shoes",
        size: "40",
        rating: 4.5,
        description: "High-performance sports shoes for all activities."
    },
    {
        id: 5,
        name: "Sports Shoes (41)",
        price: 139.99,
        image: "image/prods/Sports Shoes.webp",
        category: "Shoes",
        size: "41",
        rating: 4.5,
        description: "High-performance sports shoes for all activities."
    },
    {
        id: 6,
        name: "Sports Shoes (42)",
        price: 139.99,
        image: "image/prods/Sports Shoes.webp",
        category: "Shoes",
        size: "42",
        rating: 4.5,
        description: "High-performance sports shoes for all activities."
    },
    {
        id: 7,
        name: "Sports Shoes (43)",
        price: 139.99,
        image: "image/prods/Sports Shoes.webp",
        category: "Shoes",
        size: "43",
        rating: 4.5,
        description: "High-performance sports shoes for all activities."
    },
    {
        id: 8,
        name: "Casual Hoodies (S)",
        price: 59.99,
        image: "image/prods/Casual Hoodies.avif",
        category: "Hoodies",
        size: "S",
        rating: 5,
        description: "Soft and cozy hoodies for casual wear."
    },
    {
        id: 9,
        name: "Casual Hoodies (L)",
        price: 59.99,
        image: "image/prods/Casual Hoodies.avif",
        category: "Hoodies",
        size: "L",
        rating: 5,
        description: "Soft and cozy hoodies for casual wear."
    },
    {
        id: 10,
        name: "Black Jeans (S)",
        price: 59.99,
        image: "image/prods/Black jeans.png",
        category: "Jeans",
        size: "S",
        rating: 4,
        description: "Classic black jeans for a sleek look."
    },
    {
        id: 11,
        name: "Black Jeans (L)",
        price: 59.99,
        image: "image/prods/Black jeans.png",
        category: "Jeans",
        size: "L",
        rating: 4,
        description: "Classic black jeans for a sleek look."
    },
    {
        id: 12,
        name: "Socks (S)",
        price: 15.99,
        image: "image/prods/socks.webp",
        category: "Socks",
        size: "S",
        rating: 3.5,
        description: "Comfortable and durable socks for everyday wear."
    },
    {
        id: 13,
        name: "Socks (L)",
        price: 15.99,
        image: "image/prods/socks.webp",
        category: "Socks",
        size: "L",
        rating: 3.5,
        description: "Comfortable and durable socks for everyday wear."
    },
    {
        id: 14,
        name: "Sweatpants (S)",
        price: 80.99,
        image: "image/prods/Sweatpants.webp",
        category: "Sweatpants",
        size: "S",
        rating: 4,
        description: "Comfortable sweatpants for lounging or workouts."
    },
    {
        id: 15,
        name: "Sweatpants (L)",
        price: 80.99,
        image: "image/prods/Sweatpants.webp",
        category: "Sweatpants",
        size: "L",
        rating: 4,
        description: "Comfortable sweatpants for lounging or workouts."
    }
];

// Get product ID from URL
const urlParams = new URLSearchParams(window.location.search);
const productId = urlParams.get("id");
const product = products.find(p => p.id == productId);

if (product) {
    // Insert product details into the page
    document.getElementById("product-details").innerHTML = `
        <img src="${product.image}" alt="${product.name}" class="product-image">
        <h1 class="product-name">${product.name}</h1>
        <p class="product-price">Price: $${product.price.toFixed(2)}</p>
        <div class="product-rating">
            ${Array.from({ length: 5 }, (_, i) => `
                <i class="fa fa-star${i < Math.floor(product.rating) ? '' : '-o'}"></i>
            `).join('')}
        </div>
        <p class="product-description">${product.description}</p>

        <!-- Size Selector -->
        <div id="size-selector">
            ${product.category === "Shoes" ? `
                <!-- Shoe Sizes (39 to 43) -->
                <button class="size-option" data-size="39">39</button>
                <button class="size-option" data-size="40">40</button>
                <button class="size-option" data-size="41">41</button>
                <button class="size-option" data-size="42">42</button>
                <button class="size-option" data-size="43">43</button>
            ` : `
                <!-- Default Sizes (S and L) -->
                <button class="size-option" data-size="S">S</button>
                <button class="size-option" data-size="L">L</button>
            `}
        </div>

        <!-- Selected Size Display -->
        <p>Selected Size: <span id="selected-size">None</span></p>
    `;

    // Add event listeners for size selection
    const sizeOptions = document.querySelectorAll(".size-option");
    const selectedSizeDisplay = document.getElementById("selected-size");

    let selectedSize = null;

    sizeOptions.forEach(option => {
        option.addEventListener("click", () => {
            // Remove the "selected" class from all options
            sizeOptions.forEach(opt => opt.classList.remove("selected"));

            // Add the "selected" class to the clicked option
            option.classList.add("selected");

            // Update the selected size
            selectedSize = option.getAttribute("data-size");

            // Update the selected size display
            selectedSizeDisplay.textContent = selectedSize;
        });
    });

    // Add to Cart functionality
    window.addToCart = function () {
        if (!selectedSize) {
            alert("Please select a size before adding to cart.");
            return;
        }

        // Add the product to the cart with the selected size
        const productToAdd = {
            ...product, // Spread the existing product details
            size: selectedSize // Override the size with the selected size
        };

        let cart = JSON.parse(localStorage.getItem("cart")) || [];
        const existingProduct = cart.find(p => p.id == productId && p.size === selectedSize);

        if (existingProduct) {
            existingProduct.quantity = (existingProduct.quantity || 1) + 1; // Increment quantity
        } else {
            productToAdd.quantity = 1; // Add new product with quantity 1
            cart.push(productToAdd);
        }

        localStorage.setItem("cart", JSON.stringify(cart));
        alert("Added to cart!");
    };
} else {
    document.getElementById("product-details").innerHTML = `<p>Product not found.</p>`;
}