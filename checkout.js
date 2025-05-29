 // ✅ On DOM load
 document.addEventListener("DOMContentLoaded", () => {
    loadCartItems();
    updateTotal();

    // ✅ Payment Method Selection
    const paymentMethodSelect = document.getElementById('payment_method');
    const paymentDetailsDiv = document.getElementById('paymentDetails');

    paymentMethodSelect.addEventListener('change', () => {
      const method = paymentMethodSelect.value;
      paymentDetailsDiv.innerHTML = '';

      if (method === 'GooglePay' || method === 'Paytm' || method === 'PhonePe') {
        // UPI Payment Instructions
        paymentDetailsDiv.innerHTML = `
          <p>Click below to pay using ${method}:</p>
          <button type="button" onclick="initiateUPIPayment('${method}')">
            Pay with ${method}
          </button>
        `;
      } else if (method === 'Cards') {
        // Card Payment Fields
        paymentDetailsDiv.innerHTML = `
          <h3>Enter Card Details</h3>
          <input type="text" name="card_name" placeholder="Name on Card" required>
          <input type="text" name="card_number" placeholder="Card Number" required>
          <input type="text" name="card_expiry" placeholder="Expiry (MM/YY)" required>
          <input type="text" name="card_cvv" placeholder="CVV" required>
        `;
      } else if (method === 'NetBanking') {
        // Net Banking Options
        paymentDetailsDiv.innerHTML = `
          <h3>Select Your Bank</h3>
          <select name="bank_name" required>
            <option value="">--Select Bank--</option>
            <option value="SBI">State Bank of India</option>
            <option value="HDFC">HDFC Bank</option>
            <option value="ICICI">ICICI Bank</option>
            <option value="Axis">Axis Bank</option>
          </select>
        `;
      } else if (method === 'COD') {
        // COD - no extra fields
        paymentDetailsDiv.innerHTML = `
          <p>You will pay in cash upon delivery.</p>
        `;
      }
    });

    // ✅ Complete Order Event
    document.getElementById('checkoutForm').addEventListener('submit', async (e) => {
      e.preventDefault();

      const email = document.querySelector('input[name="email"]').value;
      const totalAmount = parseFloat(document.getElementById('total').innerText.replace('₹', ''));
      const paymentMethod = document.querySelector('#payment_method').value;
      const address = document.querySelector('input[name="address"]').value;
      const city = document.querySelector('input[name="city"]').value;
      const state = document.querySelector('input[name="state"]').value;
      const postalCode = document.querySelector('input[name="postal_code"]').value;
      const phone = document.querySelector('input[name="phone"]').value;
      const cartItems = JSON.parse(localStorage.getItem('cart')) || [];

      if (!email || !totalAmount || !address || !city || !postalCode || !phone || cartItems.length === 0) {
        alert("All fields are required!");
        return;
      }

      // ✅ If UPI, you might want to do UPI first, then place order
      if (paymentMethod === 'GooglePay' || paymentMethod === 'Paytm' || paymentMethod === 'PhonePe') {
        initiateUPIPayment(paymentMethod);
        // Optionally, return here if you want to wait for UPI success before placing order
        // return;
      }

      // ✅ Prepare data for place_order.php
      const data = new URLSearchParams();
      data.append('email', email);
      data.append('total_amount', totalAmount);
      data.append('payment_method', paymentMethod);
      data.append('address', address);
      data.append('city', city);
      data.append('state', state);
      data.append('postal_code', postalCode);
      data.append('phone', phone);
      data.append('cart_items', JSON.stringify(cartItems));

      console.log("DATA TO SEND:", Object.fromEntries(data)); // Debugging

      try {
        const response = await fetch('admin/place_order.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: data
        });

        const result = await response.json();
        console.log("RESPONSE:", result); // Debugging

        if (result.success) {
          alert(result.message);
          localStorage.removeItem('cart'); // Clear cart after success
          window.location.href = 'index.html'; // Redirect to home page
        } else {
          alert(`Error: ${result.message}`);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Failed to place order. Please try again.');
      }
    });
  });

  // ✅ UPI Payment Initiation
  function initiateUPIPayment(appName) {
    const upiId = 'merchant_vpa@bank';    // Replace with your UPI ID
    const merchantName = 'MerchantName';  // Replace with your Merchant Name
    const transactionRefId = 'TXN123456'; // Unique transaction ref
    const amount = parseFloat(document.getElementById('total').innerText.replace('₹', '')) || 0;
    const transactionNote = 'Purchase at Shreenath Pooja Store';

    // Example: upi://pay?pa=...&pn=...&tr=...&am=...&cu=INR&tn=...
    const upiUrl = `upi://pay?pa=${upiId}&pn=${merchantName}&tr=${transactionRefId}&am=${amount}&cu=INR&tn=${transactionNote}`;

    // Redirect user to UPI app
    window.location.href = upiUrl;
  }

  // ✅ Load Cart Items on Checkout Page
function loadCartItems() {
  const cartItems = JSON.parse(sessionStorage.getItem('checkoutCart')) || [];
  const cartContainer = document.getElementById('cart-items');

  cartContainer.innerHTML = "";

  if (cartItems.length === 0) {
    cartContainer.innerHTML = `<p>Your cart is empty!</p>`;
  } else {
    cartItems.forEach((item, index) => {
      const cartItem = document.createElement('div');
      cartItem.classList.add('cart-items');
      cartItem.innerHTML = `
        <p>${item.name} - ₹${item.price} x ${item.quantity}</p>
        <button onclick="removeFromCart(${index})">Remove</button>
      `;
      cartContainer.appendChild(cartItem);
    });
  }
}


  // ✅ Remove from Cart
function removeFromCart(index) {
  let cart = JSON.parse(sessionStorage.getItem('checkoutCart')) || [];
  cart.splice(index, 1);
  sessionStorage.setItem('checkoutCart', JSON.stringify(cart)); // Update sessionStorage
  loadCartItems();
  updateTotal();
}


 // ✅ Update Total Amount
function updateTotal() {
  const cartItems = JSON.parse(sessionStorage.getItem('checkoutCart')) || [];
  const total = cartItems.reduce((sum, item) => sum + (item.price * item.quantity), 0);
  document.getElementById('subtotal').innerText = `₹${total.toFixed(2)}`;
}
