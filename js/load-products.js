document.addEventListener("DOMContentLoaded", () => {
    fetchProducts();
});

async function fetchProducts() {
    try {
        const response = await fetch('/dawProject/php/get_products.php');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        if (data.success) {
            renderProducts(data.data);
            if (typeof initFilters === 'function') {
                initFilters(data.data);
            }
        } else {
            showError(data.error || "Failed to load products");
        }
    } catch (error) {
        console.error("Error:", error);
        showError(error.message || "Network error loading products");
    }
}

function renderProducts(products) {
    const featuredContainer = document.getElementById('featured-products');
    const allProductsContainer = document.getElementById('all-products');
    
    if (!featuredContainer || !allProductsContainer) {
        console.error("Product containers not found");
        return;
    }

    // Clear containers first
    featuredContainer.innerHTML = '';
    allProductsContainer.innerHTML = '';

    // Featured products (first 4)
    const featured = products.slice(0, 4);
    featured.forEach(product => {
        const card = createProductCard(product);
        if (card) {
            featuredContainer.appendChild(card);
        }
    });

    // All products
    products.forEach(product => {
        const card = createProductCard(product);
        if (card) {
            allProductsContainer.appendChild(card);
        }
    });
}

function createProductCard(product) {
    if (!product) return null;
    
    const card = document.createElement('div');
    card.className = 'product-card';
    card.dataset.category = product.category;
    card.dataset.sizes = product.variants ? product.variants.map(v => v.size).join(',') : '';
    
    card.innerHTML = `
        <a href="product_details.html?id=${product.id}">
            <img class="childprods" src="${product.image_path}" alt="${product.name}">
            <h3>${product.name}</h3>
            <div class="product-info">
                <span class="price">$${product.price.toFixed(2)}</span>
                <span class="stock ${product.total_stock > 0 ? 'in-stock' : 'out-of-stock'}">
                    ${product.total_stock > 0 ? 'In Stock' : 'Out of Stock'}
                </span>
            </div>
            <p class="category">${product.category}</p>
        </a>
        <button class="add-to-cart" 
                data-product-id="${product.id}" 
                ${product.total_stock <= 0 ? 'disabled' : ''}>
            ${product.total_stock > 0 ? 'Add to Cart' : 'Out of Stock'}
        </button>
    `;
    
    // Add event listener for Add to Cart button
    const addToCartBtn = card.querySelector('.add-to-cart');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            addToCart(product);
        });
    }
    
    return card;
}

function addToCart(product) {
    // Basic implementation - you might want to enhance this for size selection
    const cartItem = {
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image_path,
        quantity: 1
    };

    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingItem = cart.find(item => item.id === product.id);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push(cartItem);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert(`${product.name} added to cart!`);
}

function showError(message) {
    const containers = ['featured-products', 'all-products'];
    containers.forEach(id => {
        const container = document.getElementById(id);
        if (container) {
            container.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <p>${message}</p>
                </div>
            `;
        }
    });
}