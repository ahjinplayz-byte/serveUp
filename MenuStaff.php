<?php
session_start();
require_once "db.php";

$success_message = "";
$error_message = "";

// Handle Add Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $image_path = trim($_POST['image_path']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO menu_items (name, description, price, image_path, category, is_available) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdssi", $name, $description, $price, $image_path, $category, $is_available);
        
        if ($stmt->execute()) {
            $success_message = "Menu item added successfully!";
        } else {
            $error_message = "Error adding item: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Name and price are required!";
    }
}

// Handle Update Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update') {
    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = trim($_POST['category']);
    $image_path = trim($_POST['image_path']);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    if (!empty($name) && $price > 0) {
        $stmt = $conn->prepare("UPDATE menu_items SET name = ?, description = ?, price = ?, image_path = ?, category = ?, is_available = ? WHERE id = ?");
        $stmt->bind_param("ssdssii", $name, $description, $price, $image_path, $category, $is_available, $id);
        
        if ($stmt->execute()) {
            $success_message = "Menu item updated successfully!";
        } else {
            $error_message = "Error updating item: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "Name and price are required!";
    }
}

// Handle Delete Item
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Menu item deleted successfully!";
    } else {
        $error_message = "Error deleting item: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch all menu items
$sql = "SELECT * FROM menu_items ORDER BY id DESC";
$result = $conn->query($sql);
$items = $result->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>ServeUp Menu Staff | Edit Highlight of the Day</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@400;500;600&display=swap"
      rel="stylesheet"
    />

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="logo.png" />
    <link rel="stylesheet" href="CSS/Menustaff.css" />
</head>

<body class="staff-page">
    <!-- HEADER -->
    <header class="staff-header">
      <div class="header-title">
        <img src="logo.png" alt="ServeUp Logo" />
        <span>ServeUp</span>
      </div>

      <nav>
        <a href="admin.php"><i class="fa-solid fa-globe"></i> Back to admin</a>
      </nav>

      <div class="header-right">
        <span class="staff-badge">
          <i class="fa-solid fa-user-shield"></i> Staff Mode
        </span>
        <span class="staff-badge">
          <i class="fa-solid fa-user-shield"></i> <a href="staff.php">Highlight</a>
        </span>
        <span class="staff-badge">
          <i class="fa-solid fa-sign-out-alt"></i>
          <a href="logout.php">Logout</a>
        </span>
      </div>
    </header>

    <!-- MAIN EDITOR SECTION -->
    <main class="staff-container">
      <div class="section-heading">
        <p>MANAGEMENT CONSOLE</p>
        <h2>Edit Menu of the Day</h2>
        <span>Update today's featured items displayed on the menu.</span>
      </div>

      <!-- Messages -->
      <?php if (!empty($success_message)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success_message); ?>
        </div>
      <?php endif; ?>
      
      <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error_message); ?>
        </div>
      <?php endif; ?>

      <form method="POST" id="highlight-form">
        <input type="hidden" name="action" id="actionInput" value="add">
        <input type="hidden" name="id" id="itemIdInput" value="">

        <!-- Container where dynamic cards render -->
        <div id="cards-container" class="staff-grid">
          <?php if (count($items) > 0): ?>
            <?php foreach ($items as $item): ?>
              <div class="staff-card" data-id="<?php echo intval($item['id']); ?>">
                <div class="card-header">
                  <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                  <div class="card-actions">
                    <button type="button" class="btn-edit" onclick="editItem(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                      <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn-delete" onclick="deleteItem(<?php echo intval($item['id']); ?>)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>

                <div class="card-image">
                  <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                  <?php else: ?>
                    <img src="images/placeholder.jpg" alt="<?php echo htmlspecialchars($item['name']); ?>" />
                  <?php endif; ?>
                </div>

                <div class="card-details">
                  <p class="category"><?php echo htmlspecialchars($item['category']); ?></p>
                  <p class="description"><?php echo htmlspecialchars($item['description']); ?></p>
                  <p class="price">₱<?php echo number_format($item['price'], 2); ?></p>
                  <div class="availability">
                    <span class="badge <?php echo $item['is_available'] ? 'badge-available' : 'badge-unavailable'; ?>">
                      <?php echo $item['is_available'] ? '✓ Available' : '✗ Unavailable'; ?>
                    </span>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="no-items">No menu items found. Add your first item below!</p>
          <?php endif; ?>
        </div>

        <!-- Form Controls -->
        <div class="form-actions">
          <button type="button" id="add-item-btn" class="btn" onclick="showAddForm()">
            <i class="fa-solid fa-plus"></i> Add Item
          </button>
          <button type="button" class="btn btn-secondary" onclick="hideAddForm()" id="cancel-btn" style="display: none;">
            <i class="fa-solid fa-times"></i> Cancel
          </button>
        </div>

        <!-- Add/Edit Item Form -->
        <div id="item-form-container" class="item-form-container" style="display: none;">
          <div class="item-form">
            <h3>Add New Menu Item</h3>
            
            <div class="form-row">
              <div class="form-group">
                <label for="itemName">Item Name *</label>
                <input type="text" id="itemName" name="name" required placeholder="e.g., Grilled Salmon">
              </div>
              
              <div class="form-group">
                <label for="itemPrice">Price (₱) *</label>
                <input type="number" id="itemPrice" name="price" step="0.01" min="0" required placeholder="0.00">
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="itemCategory">Category</label>
                <input type="text" id="itemCategory" name="category" placeholder="e.g., Main Course">
              </div>

              <div class="form-group">
                <label for="itemImagePath">Image Path</label>
                <input type="text" id="itemImagePath" name="image_path" placeholder="e.g., images/item.jpg">
              </div>
            </div>

            <div class="form-group full-width">
              <label for="itemDescription">Description</label>
              <textarea id="itemDescription" name="description" rows="4" placeholder="Describe the menu item..."></textarea>
            </div>

            <div class="form-group checkbox">
              <input type="checkbox" id="itemAvailable" name="is_available" checked>
              <label for="itemAvailable">Available</label>
            </div>

            <div class="form-buttons">
              <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Item
              </button>
              <button type="button" class="btn btn-secondary" onclick="hideAddForm()">
                <i class="fas fa-times"></i> Cancel
              </button>
            </div>
          </div>
        </div>
      </form>
    </main>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
      <div class="modal-content">
        <h3>Confirm Delete</h3>
        <p>Are you sure you want to delete this menu item?</p>
        <form method="POST" id="deleteForm">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" id="deleteItemId">
          
          <div class="modal-buttons">
            <button type="submit" class="btn btn-danger">
              <i class="fas fa-trash"></i> Delete
            </button>
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
              <i class="fas fa-times"></i> Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
    <script src="JS/MENUStaff.js"></script>
</body>
</html>