/* =========================================
   STAFF PORTAL IMAGE PREVIEWS & DRAG-AND-DROP
========================================= */

document.addEventListener("DOMContentLoaded", () => {
  initImageUploads();
});

function initImageUploads() {
  const dropZones = document.querySelectorAll(".drop-zone");

  dropZones.forEach((zone, index) => {
    const fileInput = zone.querySelector(".file-input");
    const previewImg = document.getElementById(`preview-${index}`);
    const removeBtn = zone.querySelector(".remove-photo-btn");
    const existingImgInput = zone.querySelector(
      `input[name="existing-img-${index}"]`,
    );

    if (!fileInput || !previewImg) return;

    // Handle file selection from input
    fileInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) handleImageFile(file, zone, previewImg);
    });

    // Handle drag events
    zone.addEventListener("dragover", (e) => {
      e.preventDefault();
      zone.classList.add("dragover");
    });

    zone.addEventListener("dragleave", () => {
      zone.classList.remove("dragover");
    });

    zone.addEventListener("drop", (e) => {
      e.preventDefault();
      zone.classList.remove("dragover");

      if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        fileInput.files = e.dataTransfer.files;
        handleImageFile(e.dataTransfer.files[0], zone, previewImg);
      }
    });

    // Handle image removal / resetting field
    if (removeBtn) {
      removeBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        previewImg.src = "";
        previewImg.style.display = "none";
        fileInput.value = "";
        if (existingImgInput) existingImgInput.value = "";
        zone.classList.remove("has-image");
      });
    }
  });
}

function handleImageFile(file, zone, previewImg) {
  if (!file.type.startsWith("image/")) {
    alert("Please upload a valid image file.");
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    previewImg.src = e.target.result;
    previewImg.style.display = "block";
    zone.classList.add("has-image");
  };
  reader.readAsDataURL(file);
} /* =========================================
   STAFF PORTAL IMAGE PREVIEWS & DRAG-AND-DROP
========================================= */

document.addEventListener("DOMContentLoaded", () => {
  initImageUploads();
});

function initImageUploads() {
  const dropZones = document.querySelectorAll(".drop-zone");

  dropZones.forEach((zone, index) => {
    const fileInput = zone.querySelector(".file-input");
    const previewImg = document.getElementById(`preview-${index}`);
    const removeBtn = zone.querySelector(".remove-photo-btn");
    const existingImgInput = zone.querySelector(
      `input[name="existing-img-${index}"]`,
    );

    if (!fileInput || !previewImg) return;

    // Handle file selection from input
    fileInput.addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) handleImageFile(file, zone, previewImg);
    });

    // Handle drag events
    zone.addEventListener("dragover", (e) => {
      e.preventDefault();
      zone.classList.add("dragover");
    });

    zone.addEventListener("dragleave", () => {
      zone.classList.remove("dragover");
    });

    zone.addEventListener("drop", (e) => {
      e.preventDefault();
      zone.classList.remove("dragover");

      if (e.dataTransfer.files && e.dataTransfer.files[0]) {
        fileInput.files = e.dataTransfer.files;
        handleImageFile(e.dataTransfer.files[0], zone, previewImg);
      }
    });

    // Handle image removal / resetting field
    if (removeBtn) {
      removeBtn.addEventListener("click", (e) => {
        e.stopPropagation();
        previewImg.src = "";
        previewImg.style.display = "none";
        fileInput.value = "";
        if (existingImgInput) existingImgInput.value = "";
        zone.classList.remove("has-image");
      });
    }
  });
}

function handleImageFile(file, zone, previewImg) {
  if (!file.type.startsWith("image/")) {
    alert("Please upload a valid image file.");
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    previewImg.src = e.target.result;
    previewImg.style.display = "block";
    zone.classList.add("has-image");
  };
  reader.readAsDataURL(file);
}
