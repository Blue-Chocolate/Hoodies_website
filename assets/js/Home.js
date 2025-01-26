const productsSection = document.getElementById("product");
const shoppingcart = document.getElementById("shoppingcart");
const countDisplay = document.getElementById("Start");
let shoppingCart = [];

// Add to cart function
function addToCart(productId) {
  const product = document.querySelector(`[data-id="${productId}"]`);
  const name = product.querySelector("h3").textContent;
  const price = parseFloat(product.querySelector(".price").textContent.replace("$", ""));
  const image = product.querySelector("img").src;

  shoppingCart.push({ name, price, image });
  countDisplay.textContent = shoppingCart.length;
  displayCart();
}

// Display the shopping cart
function displayCart() {
  const cartItems = document.getElementById("cart-items");
  cartItems.innerHTML = "";
  let totalPrice = 0;

  shoppingCart.forEach((product, i) => {
    const itemDiv = document.createElement("div");
    itemDiv.innerHTML = `
      <div class="cart-item">
        <img src="${product.image}" alt="${product.name}" />
        <p>${product.name}</p>
        <p>$${product.price.toFixed(2)}</p>
        <button onclick="removeFromCart(${i})">Remove</button>
      </div>
    `;
    cartItems.appendChild(itemDiv);
    totalPrice += product.price;
  });

  document.getElementById("cart-total").textContent = `Total: $${totalPrice.toFixed(2)}`;
}

// Remove item from cart
function removeFromCart(index) {
  shoppingCart.splice(index, 1);
  displayCart();
  countDisplay.textContent = shoppingCart.length;
}

// Open shopping cart
function openShoppingCart() {
  shoppingcart.style.display = "block";
  displayCart();
}

// Close shopping cart
function closeCart() {
  shoppingcart.style.display = "none";
}

// Checkout function
function checkout() {
  if (shoppingCart.length === 0) {
    alert("Your cart is empty!");
    return;
  }
  // Redirect to checkout.php
  window.location.href = "checkout.php";


}
let cart = [];

function addToCart(productId) {
    fetch('addToCart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ productId }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Product added to cart!');
                updateCartUI(data.cart);
            } else {
                alert(data.message || 'Failed to add product to cart.');
            }
        });
}

function updateCartUI(cart) {
    const cartItemsContainer = document.querySelector('.cart-items');
    cartItemsContainer.innerHTML = '';

    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.innerHTML = `
            <p>${item.name} - $${item.price} x ${item.quantity}</p>
        `;
        cartItemsContainer.appendChild(cartItem);
    });
}

// Function to add a product to the cart
function addToCart(productId) {
    // Fetch product details (you can use AJAX to fetch from the server if needed)
    const product = {
        id: productId,
        name: document.querySelector(`.product[data-id="${productId}"] .card-title`).innerText,
        price: parseFloat(document.querySelector(`.product[data-id="${productId}"] .card-text strong`).innerText.replace('$', '')),
        quantity: 1 // Default quantity
    };

    // Check if the product is already in the cart
    const existingProduct = cart.find(item => item.id === productId);
    if (existingProduct) {
        existingProduct.quantity += 1; // Increase quantity if already in cart
    } else {
        cart.push(product); // Add new product to cart
    }

    // Update the cart display
    updateCartDisplay();
}

// Function to update the cart display
function updateCartDisplay() {
    const cartItems = document.querySelector('.cart-items');
    cartItems.innerHTML = ''; // Clear existing cart items

    // Add each item to the cart display
    cart.forEach(item => {
        const cartItem = document.createElement('div');
        cartItem.className = 'cart-item';
        cartItem.innerHTML = `
            <p>${item.name} - $${item.price.toFixed(2)} x ${item.quantity}</p>
        `;
        cartItems.appendChild(cartItem);
    });

    // Update the cart count
    document.getElementById('Start').innerText = cart.length;
}

// Function to open the shopping cart
function openShoppingCart() {
    document.getElementById('shoppingcart').style.display = 'block';
}

// Function to close the shopping cart
function closeCart() {
    document.getElementById('shoppingcart').style.display = 'none';
}

// Function to handle checkout
function checkout() {
    alert('Checkout functionality not implemented yet.');
    // You can add logic to process the cart items here
}
