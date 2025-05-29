// EXACT SAME CORE CODE
let cart = JSON.parse(localStorage.getItem('cart')) || [];
let wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
let products = [];
let filteredProducts = [];
let currentPage = 1;
const productsPerPage = 9;
const category = document.body.getAttribute('data-category');

async function fetchProducts() {
    try {
        const res = await fetch(`fetch_products.php?category=${category}`);
        if (!res.ok) throw new Error("Failed to fetch products");
        const data = await res.json();

        products = data;
        filteredProducts = [...products];
        displayProducts(filteredProducts);
        updateCartCount();
        updateWishlistCount();
    } catch (error) {
        console.error("Error fetching products:", error);
    }
}

// ✅ Display Products
function displayProducts(productsToDisplay) {
    const productList = document.getElementById("product-list");
    productList.innerHTML = "";

    const startIndex = (currentPage - 1) * productsPerPage;
    const endIndex = startIndex + productsPerPage;
    const paginatedProducts = productsToDisplay.slice(startIndex, endIndex);

    paginatedProducts.forEach((product) => {
        const productCard = document.createElement("div");
        productCard.classList.add("product-card");

        productCard.innerHTML = `
            <div class="product-image-container">
                <img class="product-image" src="${product.image}" alt="${product.name}">
                <img class="product-hover-image" src="${product.hoverImage}" alt="${product.name}">
            </div>
            <h3>${product.name}</h3>
            <p>Price: ₹${product.price}</p>
            <p>Weight: ${product.weight}</p>
            <button class="add-to-cart-btn" data-id="${product.id}">Add to Cart</button>
            <button class="wishlist-btn" data-id="${product.id}">❤️</button>
        `;
        productList.appendChild(productCard);
    });

    displayPagination(productsToDisplay.length);
}

// ✅ Add to Cart (Fixed with user-specific cart key)
function addToCart(productId) {
    const product = products.find(p => p.id == productId);
    if (product) {
        const existingItem = cart.find(item => item.id == productId);
        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({ ...product, quantity: 1 });
        }

        // ✅ Save using user-specific key
        const userId = localStorage.getItem('loggedInUser') || 'guest';
        localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));

        updateCartCount(); 
        showNotification("✅ Product added to cart!");
    }
}


// ✅ Add to Wishlist
function addToWishlist(productId) {
    const product = products.find(p => p.id == productId);
    if (product && !wishlist.some(item => item.id == productId)) {
        wishlist.push(product);
        localStorage.setItem('wishlist', JSON.stringify(wishlist));
        updateWishlistCount();
        showNotification("❤️ Product added to wishlist!");
    }
}

// ✅ Update Cart Count
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;

    if (totalItems === 0) {
        cartCount.style.display = 'none';
    } else {
        cartCount.style.display = 'inline';
    }
}

// ✅ Update Wishlist Count
function updateWishlistCount() {
    const wishlistCount = document.getElementById('wishlist-count');
    wishlistCount.textContent = wishlist.length;

    if (wishlist.length === 0) {
        wishlistCount.style.display = 'none';
    } else {
        wishlistCount.style.display = 'inline';
    }
}

// ✅ Remove from Cart
function removeFromCart(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}

// ✅ Remove from Wishlist
function removeFromWishlist(index) {
    wishlist.splice(index, 1);
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
    updateWishlistCount();
}

// ✅ Show Notification
function showNotification(message) {
    const notification = document.createElement('div');
    notification.classList.add('notification');
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 2000);
}

// ✅ Open Cart (on Hover)
const miniCart = document.getElementById('mini-cart');
const cartIcon = document.getElementById('cart-icon');

cartIcon.addEventListener('mouseenter', () => {
    miniCart.style.display = 'block';
    miniCart.innerHTML = `<p>You have ${cart.reduce((sum, item) => sum + item.quantity, 0)} items in your cart</p>`;
});

cartIcon.addEventListener('mouseleave', () => {
    miniCart.style.display = 'none';
});

// ✅ Open Wishlist (on Hover)
const wishlistContainer = document.getElementById('wishlist-container');
const wishlistIcon = document.getElementById('wishlist-icon');

wishlistIcon.addEventListener('mouseenter', () => {
    wishlistContainer.style.display = 'block';
    wishlistContainer.innerHTML = `<p>You have ${wishlist.length} items in your wishlist</p>`;
});

wishlistIcon.addEventListener('mouseleave', () => {
    wishlistContainer.style.display = 'none';
});

// ✅ Open Cart Page on Click
cartIcon.addEventListener('click', () => {
    window.location.href = 'cart.html';
});

// ✅ Open Wishlist Page on Click
wishlistIcon.addEventListener('click', () => {
    window.location.href = 'wishlist.html';
});

// ✅ Load Cart & Wishlist from LocalStorage on Page Load
document.addEventListener("DOMContentLoaded", () => {
    cart = JSON.parse(localStorage.getItem('cart')) || [];
    wishlist = JSON.parse(localStorage.getItem('wishlist')) || [];
    
    fetchProducts(); // ✅ Products load karayla
    updateCartCount(); // ✅ Cart count update karnar
    updateWishlistCount(); // ✅ Wishlist count update karnar

    // >>> BRAND FILTER SNIPPET >>>
    document.querySelectorAll('.filter-btn').forEach(button => {
      button.addEventListener('click', () => {
        const brand = button.getAttribute('data-brand');
        if (brand === 'all') {
          filteredProducts = [...products];
        } else {
          filteredProducts = products.filter(p => p.brand === brand);
        }
        currentPage = 1;
        displayProducts(filteredProducts);
      });
    });
    // <<< END BRAND FILTER SNIPPET <<<

    // >>> TYPE FILTER SNIPPET >>>
    document.querySelectorAll('.type-filter-btn').forEach(button => {
      button.addEventListener('click', () => {
        const typeVal = button.getAttribute('data-type');
        if (typeVal === '') {
          filteredProducts = [...products];
        } else {
          filteredProducts = products.filter(p => p.type === typeVal);
        }
        currentPage = 1;
        displayProducts(filteredProducts);
      });
    });
    // <<< END TYPE FILTER SNIPPET <<<
});

// ✅ Pagination Display
function displayPagination(totalProducts) {
    const totalPages = Math.ceil(totalProducts / productsPerPage);
    const pagination = document.getElementById("pagination");
    pagination.innerHTML = "";

    for (let i = 1; i <= totalPages; i++) {
        const button = document.createElement("button");
        button.textContent = i;
        button.classList.add("pagination-btn");
        if (i === currentPage) button.classList.add("active");
        button.addEventListener("click", () => {
            currentPage = i;
            displayProducts(filteredProducts);
        });
        pagination.appendChild(button);
    }
}

// ✅ Fix Add to Cart Issue
document.addEventListener('click', (event) => {
    if (event.target.classList.contains('add-to-cart-btn')) {
        addToCart(event.target.getAttribute('data-id'));
    }
    if (event.target.classList.contains('wishlist-btn')) {
        addToWishlist(event.target.getAttribute('data-id'));
    }
});

// ✅ Category value console madhun check kar
console.log("Category:", category);
