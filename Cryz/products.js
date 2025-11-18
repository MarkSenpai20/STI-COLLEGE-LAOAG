document.addEventListener("DOMContentLoaded", () => {
  const productListContainer = document.getElementById(
    "product-list-container"
  );
  const addProductForm = document.getElementById("add-product-form");
  const newProductName = document.getElementById("new-product-name");
  const newProductPrice = document.getElementById("new-product-price");
  const loadingMessage = document.getElementById("product-list-loading");

  /**
   * Renders a single product item in the list
   */
  function renderProduct(product) {
    const productRow = document.createElement("div");
    productRow.className = "product-manage-row";
    productRow.dataset.id = product.id;

    productRow.innerHTML = `
            <span>${product.item_name} - P${parseFloat(
      product.price_per_item
    ).toFixed(2)}</span>
            <button class="btn-delete-product" data-id="${
              product.id
            }">&times;</button>
        `;

    // Add delete event listener
    productRow
      .querySelector(".btn-delete-product")
      .addEventListener("click", deleteProduct);

    productListContainer.appendChild(productRow);
  }

  /**
   * Fetches all products from the API and renders them
   */
  async function loadProducts() {
    try {
      const response = await fetch("api.php?action=manage_products");
      if (!response.ok) throw new Error("Network response was not ok");

      const products = await response.json();

      loadingMessage.style.display = "none";
      productListContainer.innerHTML = ""; // Clear list

      if (products.length === 0) {
        productListContainer.innerHTML =
          '<div class="item-row empty">No fixed products saved.</div>';
      } else {
        products.forEach(renderProduct);
      }
    } catch (error) {
      console.error("Failed to load products:", error);
      loadingMessage.textContent = "Failed to load products.";
    }
  }

  /**
   * Handles the submission of the "Add Product" form
   */
  async function addProduct(event) {
    event.preventDefault();

    const formData = new FormData(addProductForm);

    try {
      const response = await fetch("api.php?action=manage_products", {
        method: "POST",
        body: formData,
      });

      if (!response.ok) throw new Error("Failed to save product");

      const newProduct = await response.json();

      // Clear "empty" message if it exists
      const emptyMessage =
        productListContainer.querySelector(".item-row.empty");
      if (emptyMessage) emptyMessage.remove();

      renderProduct(newProduct); // Add the new product to the list

      // Clear form
      newProductName.value = "";
      newProductPrice.value = "";
    } catch (error) {
      console.error("Failed to add product:", error);
      alert("Error: Could not save product.");
    }
  }

  /**
   * Handles the click of a "Delete" button
   */
  async function deleteProduct(event) {
    const productId = event.target.dataset.id;

    // if (!confirm("Are you sure you want to delete this product?")) {
    //     return;
    // }

    try {
      const response = await fetch(
        `api.php?action=manage_products&id=${productId}`,
        {
          method: "DELETE",
        }
      );

      if (!response.ok) throw new Error("Failed to delete product");

      const result = await response.json();

      if (result.success) {
        // Remove the item from the DOM
        const row = event.target.closest(".product-manage-row");
        row.remove();
      } else {
        throw new Error(result.message || "Unknown error");
      }
    } catch (error) {
      console.error("Failed to delete product:", error);
      alert("Error: Could not delete product.");
    }
  }

  // --- Initialize ---
  addProductForm.addEventListener("submit", addProduct);
  loadProducts();
});
