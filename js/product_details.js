document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');
    
    if (productId) {
        loadProductDetails(productId);
    } else {
        showError('Product ID not found');
    }
});

async function loadProductDetails(productId) {
    try {
        const response = await fetch(`../php/get_product.php?id=${productId}`);
        const data = await response.json();
        
        if (data.success) {
            renderProductDetails(data.data);
        } else {
            throw new Error(data.error || 'Failed to load product details');
        }
    } catch (error) {
        showError(error.message);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = parseInt(urlParams.get('id'));
    const productContainer = document.getElementById('product-details');

    if (!productId || isNaN(productId)) {
        showError("Invalid product specified");
        return;
    }

    fetchProductDetails(productId);

    async function fetchProductDetails(id) {
        try {
            const response = await fetch(`/dawProject/php/get_product.php?id=${id}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (!result.success || !result.data) {
                throw new Error("Product not found");
            }

            renderProduct(result.data);
        } catch (error) {
            console.error("Error loading product:", error);
            showError(error.message || "Failed to load product details");
        }
    }

    function renderProduct(product) {
        productContainer.innerHTML = `  
            <div class="product-image-container">
                <img src="${product.image_path}" 
                     alt="${product.name}" 
                     class="product-image">
            </div>
            
            <div class="product-info">
                <h1 class="product-name">${product.name}</h1>
                <p class="product-category">${product.category}</p>
                
                <div class="product-price-stock">
                    <span class="product-price">$${parseFloat(product.price).toFixed(2)}</span>
                    <span class="stock-status ${product.total_stock > 0 ? 'in-stock' : 'out-of-stock'}">
                        ${product.total_stock > 0 ? `${product.total_stock} available` : 'Out of stock'}
                    </span>
                </div>
                
                ${product.variants.length > 0 ? `
                <div class="size-selector" id="size-selector">
                    <label>Size:</label>
                    <select id="size-select">
                        ${product.variants.map(variant => 
                            `<option value="${variant.size}" 
                                     data-stock="${variant.stock}"
                                     ${variant.stock <= 0 ? 'disabled' : ''}>
                                ${variant.size} ${variant.stock <= 0 ? '(Out of Stock)' : ''}
                            </option>`
                        ).join('')}
                    </select>
                    <div class="stock-info" id="size-stock-display">
                        Stock: ${product.variants[0].stock}
                    </div>
                </div>` : ''}
                
                <p class="product-description">${product.description}</p>
                
                <div class="button-container">
                    <button class="add-to-cart-btn" ${product.total_stock <= 0 ? 'disabled' : ''}>
                        ${product.total_stock > 0 ? 'Add to Cart' : 'Out of Stock'}
                    </button>
                </div>
            </div>
        `;

        // Update stock display when size changes
        document.getElementById('size-select')?.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const stockDisplay = document.getElementById('size-stock-display');
            if (stockDisplay) {
                stockDisplay.textContent = `Stock: ${selectedOption.dataset.stock}`;
            }
        });

        // Add to cart button
        document.querySelector('.add-to-cart-btn')?.addEventListener('click', () => {
            addToCart(product);
        });
    }

    function addToCart(product) {
        const sizeSelect = document.getElementById('size-select');
        const selectedSize = sizeSelect?.value || '';
        const selectedVariant = sizeSelect ? 
            product.variants.find(v => v.size === selectedSize) : null;
        
        if (selectedVariant && selectedVariant.stock <= 0) {
            alert("This size is out of stock!");
            return;
        }

        const cartItem = {
            id: product.id,
            product_variant_id: selectedVariant?.id || null, // Ensure variant_id is included or null
            name: product.name,
            price: product.price,
            size: selectedSize,
            image: product.image_path,
            quantity: 1
        };

        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const existingItem = cart.find(item => 
            item.id === product.id && item.size === selectedSize
        );

        if (existingItem) {
            if (selectedVariant && existingItem.quantity >= selectedVariant.stock) {
                alert(`Only ${selectedVariant.stock} available in this size!`);
                return;
            }
            existingItem.quantity += 1;
        } else {
            cart.push(cartItem);
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        alert(`${product.name} (${selectedSize}) added to cart!`);
    }

    // Rest of your existing error function...
});