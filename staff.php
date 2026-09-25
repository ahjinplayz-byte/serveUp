<?php
require_once 'db.php';

$message = "";

// Handle form submission to update MySQL database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 0; $i < 3; $i++) {
        $id = $i + 1;
        $name = $_POST["name-$i"] ?? '';
        $desc = $_POST["desc-$i"] ?? '';
        $price = $_POST["price-$i"] ?? 0;
        $rating = $_POST["rating-$i"] ?? 0;
        
        $image_path = $_POST["existing-img-$i"] ?? '';

        // Handle Image Upload
        if (isset($_FILES["photo-$i"]) && $_FILES["photo-$i"]['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_tmp = $_FILES["photo-$i"]['tmp_name'];
            $file_name = time() . '_' . basename($_FILES["photo-$i"]['name']);
            $target_file = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                // Save clean relative path from root
                $image_path = 'uploads/' . $file_name; 
            }
        }

        // Check if row exists, insert if missing
        $check_stmt = $conn->prepare("SELECT id FROM highlights WHERE id = ?");
        $check_stmt->bind_param("i", $id);
        $check_stmt->execute();
        $check_res = $check_stmt->get_result();
        $check_stmt->close();

        if ($check_res && $check_res->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE highlights SET name = ?, description = ?, price = ?, rating = ?, image_url = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param("ssddsi", $name, $desc, $price, $rating, $image_path, $id);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO highlights (id, name, description, price, rating, image_url) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("issdds", $id, $name, $desc, $price, $rating, $image_path);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    $message = "Highlights updated successfully!";
}

// Fetch current values from MySQL
$highlights = [];
$result = $conn->query("SELECT * FROM highlights ORDER BY id ASC LIMIT 3");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $highlights[] = $row;
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ServeUp | Edit Highlight of the Day</title>

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
    <link rel="stylesheet" href="CSS/staff.css" />
  </head>

  <body class="staff-page">
    <header class="staff-header">
      <div class="header-title">
        <img src="logo.png" alt="ServeUp Logo" />
        <span>ServeUp</span>
      </div>

      <nav>
        <a href="../customer/homepage.php"><i class="fa-solid fa-globe"></i> View Website</a>
      </nav>

      <div class="header-right">
        <span class="staff-badge"><i class="fa-solid fa-user-shield"></i> Staff Mode</span>
        <span class="staff-badge"><a href="MenuStaff.php" style="color:inherit; text-decoration:none;"><i class="fa-solid fa-user-shield"></i> Menu Staff</a></span>
      </div>
    </header>

    <main class="staff-container">
      <div class="section-heading">
        <p>MANAGEMENT CONSOLE</p>
        <h2>Edit Highlight of the Day</h2>
        <span>Update today's featured items displayed on the public menu.</span>
      </div>

      <?php if (!empty($message)): ?>
        <p style="text-align: center; color: green; font-weight: bold; margin-bottom: 20px;"><?php echo $message; ?></p>
      <?php endif; ?>

      <form id="highlight-form" action="staff.php" method="POST" enctype="multipart/form-data" class="staff-grid">
        <?php for ($i = 0; $i < 3; $i++): 
            $item = $highlights[$i] ?? ['name' => '', 'description' => '', 'price' => '', 'rating' => '', 'image_url' => ''];
            $has_img = !empty($item['image_url']);
            $preview_src = $has_img ? '../' . htmlspecialchars($item['image_url']) : '';
        ?>
          <div class="staff-card">
            <div class="card-header">
              <h3>Highlight #<?php echo $i + 1; ?></h3>
            </div>

            <div class="drink-image drop-zone <?php echo $has_img ? 'has-image' : ''; ?>" id="dz-<?php echo $i; ?>">
              <i class="fa-solid fa-cloud-arrow-up upload-icon"></i>
              <span class="upload-text">Drag & drop photo or click</span>
              <input type="file" name="photo-<?php echo $i; ?>" class="file-input" accept="image/*" />
              <input type="hidden" name="existing-img-<?php echo $i; ?>" value="<?php echo htmlspecialchars($item['image_url']); ?>" />
              <img class="card-photo" id="preview-<?php echo $i; ?>" src="<?php echo $preview_src; ?>" alt="Item Preview" <?php echo $has_img ? 'style="display:block;"' : ''; ?> />
              <button type="button" class="remove-photo-btn" aria-label="Remove photo">
                <i class="fa-solid fa-xmark"></i> Change
              </button>
            </div>

            <div class="edit-fields">
              <div class="input-group">
                <label for="name-<?php echo $i; ?>">Item Name</label>
                <input type="text" name="name-<?php echo $i; ?>" id="name-<?php echo $i; ?>" class="staff-input" value="<?php echo htmlspecialchars($item['name']); ?>" required />
              </div>

              <div class="input-group">
                <label for="desc-<?php echo $i; ?>">Description</label>
                <textarea name="desc-<?php echo $i; ?>" id="desc-<?php echo $i; ?>" class="staff-input staff-textarea" rows="3" required><?php echo htmlspecialchars($item['description']); ?></textarea>
              </div>

              <div class="input-row">
                <div class="input-group">
                  <label for="price-<?php echo $i; ?>">Price (₱)</label>
                  <input type="number" step="0.01" name="price-<?php echo $i; ?>" id="price-<?php echo $i; ?>" class="staff-input" value="<?php echo htmlspecialchars($item['price']); ?>" required />
                </div>

                <div class="input-group">
                  <label for="rating-<?php echo $i; ?>">Rating</label>
                  <input type="number" step="0.1" max="5" min="0" name="rating-<?php echo $i; ?>" id="rating-<?php echo $i; ?>" class="staff-input" value="<?php echo htmlspecialchars($item['rating']); ?>" required />
                </div>
              </div>
            </div>
          </div>
        <?php endfor; ?>

        <div class="staff-actions">
          <button type="submit" class="save-btn">
            <i class="fa-solid fa-floppy-disk"></i> Save Highlights
          </button>
        </div>
      </form>
    </main>

    <script src="./JS/staff.js"></script>
  </body>
</html>