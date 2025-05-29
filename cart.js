// Initialize cart from localStorage using user-specific key
let userId = localStorage.getItem('loggedInUser') || 'guest';
let cart = JSON.parse(localStorage.getItem(`cart_${userId}`)) || [];

// Function to update the cart UI
function updateCartUI() {
  const cartItemsContainer = document.getElementById("cart-items");
  const subtotalElement = document.getElementById("subtotal");

  cartItemsContainer.innerHTML = ""; // Clear existing cart items

  let subtotal = 0;

  // Display each cart item
  cart.forEach((item, index) => {
    const row = document.createElement("tr");

    row.innerHTML = `
      <td><img src="${item.image}" alt="${item.name}" class="cart-item-image" /></td>
      <td>${item.name}</td>
      <td>₹${item.price}</td>
      <td>
        <button class="quantity-btn" onclick="updateQuantity(${index}, -1)">-</button>
        <input type="number" value="${item.quantity}" readonly />
        <button class="quantity-btn" onclick="updateQuantity(${index}, 1)">+</button>
      </td>
      <td>₹${(item.price * item.quantity).toFixed(2)}</td>
      <td><button class="remove-btn" onclick="removeItem(${index})">Remove</button></td>
    `;

    cartItemsContainer.appendChild(row);
    subtotal += item.price * item.quantity;
  });

  // Update subtotal
  subtotalElement.textContent = "₹" + subtotal.toFixed(2);
}

// Function to update the quantity of a cart item
function updateQuantity(index, change) {
  cart[index].quantity += change;

  if (cart[index].quantity < 1) cart[index].quantity = 1; // Prevent quantity from going below 1

  // Save updated cart to localStorage with user-specific key
  let userId = localStorage.getItem('loggedInUser') || 'guest';
localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));


  // Update the UI
  updateCartUI();
}

// Function to remove an item from the cart
function removeItem(index) {
  cart.splice(index, 1); // Remove the item from cart

  // Save updated cart to localStorage with user-specific key
  localStorage.setItem(`cart_${userId}`, JSON.stringify(cart));

  // Update the UI
  updateCartUI();
}

// Function to handle checkout (Now storing cart data for checkout page)
function proceedToCheckout() {
  if (cart.length === 0) {
    alert("Your cart is empty!");
    return;
  }
  console.log("Cart Data Sent to Checkout:", cart);
  // Store cart data in sessionStorage for checkout page
  sessionStorage.setItem("checkoutCart", JSON.stringify(cart));

  // Redirect to checkout page
  window.location.href = "checkout.html";
}

// Load the cart and update the UI when the page loads
document.addEventListener("DOMContentLoaded", () => {
  updateCartUI(); // Update the cart UI based on the data in localStorage

  // Checkout button event listener
  document.getElementById("checkout-btn").addEventListener("click", proceedToCheckout);
});
