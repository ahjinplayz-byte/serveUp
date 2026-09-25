<?php
require_once "db.php";

// Fetch menu items from the database (include id)
$sql = "SELECT id, name, price, image_path FROM menu_items ORDER BY id ASC";
$result = $conn->query($sql);

// Build PRODUCTS JS array (fallback)
$products_for_js = [];
if ($result && $result->num_rows > 0) {
    $result->data_seek(0);
    while ($row = $result->fetch_assoc()) {
        $products_for_js[] = [
            'id' => (int)$row['id'],
            'name' => $row['name'],
            'price' => (float)$row['price'],
            'image_path' => $row['image_path'] ?? ''
        ];
    }
    $result->data_seek(0);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ServeUp | Menu</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@400;500;600&display=swap"
      rel="stylesheet"
    />

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />

    <!-- MAIN STYLESHEET (must match your CSS file) -->
    <link rel="stylesheet" href="./CSS/menu.css" />

  </head>

  <body>
    <!-- HEADER -->
    <header class="public-header">
      <div class="logo header-title">
        <img src="logo.png" alt="ServeUp Logo" />
        <a href="homepage.php">ServeUp</a>
      </div>

      <div class="header-right">
        <button class="cart-button" onclick="showCart()" aria-label="Shopping cart">
          <i class="fa-solid fa-cart-shopping"></i>
          <span id="cart-count">0</span>
        </button>
      </div>
    </header>

    <!-- Cart Drawer & Overlay -->
    <div id="cart-overlay" class="cart-overlay" onclick="hideCart()"></div>
    <aside id="cart-modal" class="cart-modal" aria-hidden="true">
      <div class="cart-header">
        <h2>Your Cart</h2>
        <button class="close-cart" onclick="hideCart()">&times;</button>
      </div>
      <div id="cart-items" class="cart-items"></div>
      <div class="cart-footer">
        <div class="cart-total">
          <span>Subtotal:</span>
          <span id="cart-total-price">₱0</span>
        </div>
        <button class="checkout-btn" onclick="checkout()">Checkout</button>
      </div>
    </aside>

    <!-- Receipt Modal & Overlay -->
    <div id="receipt-overlay" class="cart-overlay" onclick="closeReceipt()"></div>
    <aside id="receipt-modal" class="cart-modal" aria-hidden="true">
      <div class="cart-header">
        <h2>Order Receipt</h2>
        <button class="close-cart" onclick="closeReceipt()">&times;</button>
      </div>

      <div id="receipt-items" class="receipt-body" style="padding:16px 0;"></div>

      <div class="cart-footer">
        <div class="cart-total" style="border-top:1px dashed #ccc; padding-top:12px;">
          <span>Total Paid:</span>
          <span id="receipt-total">₱0</span>
        </div>
        <button class="checkout-btn" onclick="closeReceipt()" style="margin-top:12px; background-color:#28a745;">Done</button>
      </div>
    </aside>

    <!-- MAIN -->
    <main>

      <!-- Server-rendered menu using classes from your CSS -->
      <section class="menu-container">
        <div class="menu-heading">
          <h2>Featured Menu Items</h2>
          <p>Discover today's highlighted selections crafted just for you.</p>
        </div>

        <div class="menu-grid" id="menuGrid">
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($item = $result->fetch_assoc()): ?>
              <article class="menu-card">
                <div class="card-image">
                  <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                  <?php else: ?>
                    <img src="placeholder.jpg" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                  <?php endif; ?>
                </div>

                  <div class="card-details">
                  <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                  <span class="price">₱<?php echo number_format($item['price'], 2); ?></span>

                  <div style="margin-top:10px;">
                    <button
                      class="add-btn"
                      onclick="addToCart('<?php echo addslashes($item['name']); ?>', <?php echo (float)$item['price']; ?>)"
                      aria-label="Add <?php echo htmlspecialchars($item['name']); ?> to cart"
                    >+ Add to Cart</button>
                  </div>
                </div>
              </article>
            <?php endwhile; ?>
          <?php else: ?>
            <div class="no-items">No menu items are currently available.</div>
          <?php endif; ?>
        </div>
      </section>
    </main> 

    <!-- Menu JS (ensure path/case exactly matches) -->
    <script src="./JS/Menu.js" defer></script>
  </body>
</html>