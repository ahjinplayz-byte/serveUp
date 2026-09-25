// Staff Menu Management JavaScript

// Show Add Form
function showAddForm() {
  document.getElementById('item-form-container').style.display = 'block';
  document.getElementById('add-item-btn').style.display = 'none';
  document.getElementById('cancel-btn').style.display = 'inline-flex';
  document.getElementById('actionInput').value = 'add';
  document.getElementById('itemIdInput').value = '';
  document.getElementById('highlight-form').reset();
}

// Hide Add Form
function hideAddForm() {
  document.getElementById('item-form-container').style.display = 'none';
  document.getElementById('add-item-btn').style.display = 'inline-flex';
  document.getElementById('cancel-btn').style.display = 'none';
  document.getElementById('highlight-form').reset();
}

// Edit Item
function editItem(item) {
  document.getElementById('item-form-container').style.display = 'block';
  document.getElementById('add-item-btn').style.display = 'none';
  document.getElementById('cancel-btn').style.display = 'inline-flex';
  
  document.getElementById('actionInput').value = 'update';
  document.getElementById('itemIdInput').value = item.id;
  document.getElementById('itemName').value = item.name;
  document.getElementById('itemPrice').value = item.price;
  document.getElementById('itemCategory').value = item.category;
  document.getElementById('itemImagePath').value = item.image_path;
  document.getElementById('itemDescription').value = item.description;
  document.getElementById('itemAvailable').checked = item.is_available === 1;
  
  // Change form title
  document.querySelector('.item-form h3').textContent = 'Edit Menu Item';
  
  // Scroll to form
  document.getElementById('item-form-container').scrollIntoView({ behavior: 'smooth' });
}

// Delete Item
function deleteItem(itemId) {
  document.getElementById('deleteItemId').value = itemId;
  document.getElementById('deleteModal').classList.add('show');
}

// Close Delete Modal
function closeDeleteModal() {
  document.getElementById('deleteModal').classList.remove('show');
}

// Close modal when clicking outside
window.onclick = function(event) {
  const deleteModal = document.getElementById('deleteModal');
  if (event.target === deleteModal) {
    deleteModal.classList.remove('show');
  }
}

// Handle Form Submission
document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('highlight-form');
  
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form data
    const formData = new FormData(form);
    
    // Submit form
    fetch('', {
      method: 'POST',
      body: formData
    }).then(response => {
      if (response.ok) {
        location.reload();
      }
    });
  });

  // Close alerts after 5 seconds
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.3s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 300);
    }, 5000);
  });
});