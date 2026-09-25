<?php
require_once 'db.php';

// Fetch highlights from database
$result = null;
if (!isset($conn)) {
    die("Database connection error: Unable to connect to database.");
}

$sql = "SELECT * FROM highlights ORDER BY id ASC LIMIT 3";
$result = $conn->query($sql);

if (!$result) {
    error_log("Database query failed: " . $conn->error);
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ServeUp | Crafted With Care</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@400;500;600&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />
    <link rel="icon" type="image/png" href="logo.png" />
    <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <header>
      <div class="header-title">
        <img src="logo.png" alt="ServeUp Logo" />
        <span>ServeUp</span>
      </div>

      <nav>
        <a href="#home">Home</a>
        <a href="#menu">Highlights</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="Menu.php">Menu</a>
      </nav>

      <div class="header-right">
        <button
          class="cart-button"
          onclick="showCart()"
          aria-label="Shopping cart"
        >
          <i class="fa-solid fa-cart-shopping"></i>
          <span id="cart-count">0</span>
        </button>

        <button class="book-button" onclick="bookTable()">Book Table</button>
      </div>
    </header>

    <section id="home" class="home">
      <div class="hero-overlay"></div>

      <div class="hero-content">
        <p class="hero-small">WELCOME TO SERVEUP</p>
        <h1>Served with <span>passion</span></h1>
        <p class="hero-text">
          Fresh ingredients, comforting flavors, and beautifully prepared meals
          made to create moments worth remembering.
        </p>

        <a href="#menu" class="hero-button">
          Explore Menu
          <i class="fa-solid fa-arrow-right"></i>
        </a>
      </div>
    </section>

    <section id="menu" class="highlight">
      <div class="section-heading">
        <p>TODAY'S SPECIAL</p>
        <h2>Highlight of the Day</h2>
        <span> A special creation made for you today. </span>
      </div>

      <div class="highlight-container" id="highlight-cards-container">
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php 
              $db_url = $row['image_url'];
              $image_path = '';
              if (!empty($db_url)) {
                  // Resolve path relative to customer folder
                  $image_path = (strpos($db_url, 'uploads/') === 0) ? '../' . $db_url : $db_url;
              }
            ?>
            <div class="drink-card">
              <div class="drink-image" <?php echo !empty($image_path) ? 'style="background-image: url(\'' . htmlspecialchars($image_path) . '\'); background-size: cover; background-position: center;"' : ''; ?>>
                <?php if (empty($image_path)): ?>
                  <span>☕</span>
                <?php endif; ?>
              </div>
              <div class="drink-rating">
                <i class="fa-solid fa-star"></i> <?php echo htmlspecialchars($row['rating']); ?>
              </div>
              <h3><?php echo htmlspecialchars($row['name']); ?></h3>
              <p><?php echo htmlspecialchars($row['description']); ?></p>
              <div class="drink-bottom">
                <span class="drink-price">₱<?php echo htmlspecialchars($row['price']); ?></span>
                <button onclick="addToCart('<?php echo addslashes($row['name']); ?>', <?php echo $row['price']; ?>)" aria-label="Add <?php echo htmlspecialchars($row['name']); ?>">
                  <i class="fa-solid fa-plus"></i>
                </button>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No highlights available today.</p>
        <?php endif; ?>
      </div>
    </section>

    <section id="about" class="about">
      <div class="about-image">
        <div class="about-image-placeholder">
          <i class="fa-solid fa-utensils"></i>
        </div>
      </div>

      <div class="about-content">
        <p class="section-label">OUR STORY</p>
        <h2>Your favorite <span>moment</span> begins here.</h2>
        <p>At ServeUp, we believe food is more than something you eat. It is a reason to gather, slow down, and enjoy the moment.</p>
        <p>Every dish is prepared with carefully selected ingredients and a whole lot of passion.</p>
        <a href="#contact" class="outline-button">Learn More <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </section>

    <section id="contact" class="contact">
      <div class="section-heading">
        <p>COME SAY HELLO</p>
        <h2>Contact Us</h2>
        <span> We'd love to welcome you to ServeUp. </span>
      </div>

      <div class="contact-container">
        <div class="contact-card">
          <div class="contact-icon"><i class="fa-solid fa-location-dot"></i></div>
          <h3>Location</h3>
          <p>Iloilo City, Philippines</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon"><i class="fa-solid fa-phone"></i></div>
          <h3>Phone</h3>
          <p>0912 345 6789</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
          <h3>Email</h3>
          <p>hello@serveup.com</p>
        </div>

        <div class="contact-card">
          <div class="contact-icon"><i class="fa-solid fa-clock"></i></div>
          <h3>Opening Hours</h3>
          <p>Monday - Sunday<br />10:00 AM - 9:00 PM</p>
        </div>
      </div>
    </section>

    <footer>
      <div class="footer-logo">ServeUp</div>
      <p>Crafted with care. Served with love.</p>
      <div class="social-icons">
        <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" aria-label="TikTok"><i class="fa-brands fa-tiktok"></i></a>
      </div>
      <p class="copyright">© 2026 ServeUp. All rights reserved.</p>
    </footer>

    <script src="./JS/js.js"></script>
  </body>
</html>