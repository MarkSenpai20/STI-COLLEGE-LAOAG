document.addEventListener("DOMContentLoaded", () => {
  const productSearch = document.getElementById("product-search");
  const searchResults = document.getElementById("search-results");

  // Form fields to auto-fill
  const inputItemName = document.getElementById("input-item-name");
  const inputItemPrice = document.getElementById("input-item-price");
  const inputItemQty = document.getElementById("input-item-qty");

  let searchTimeout;

  /**
   * Fetches products based on the search term
   */
  async function searchProducts(term) {
    if (term.length < 1) {
      searchResults.innerHTML = "";
      searchResults.classList.remove("active");
      return;
    }

    try {
      const response = await fetch(
        `api.php?action=search_products&term=${encodeURIComponent(term)}`
      );
      if (!response.ok) throw new Error("Search request failed");

      const products = await response.json();

      searchResults.innerHTML = ""; // Clear old results
      if (products.length > 0) {
        products.forEach((product) => {
          const item = document.createElement("div");
          item.className = "search-result-item";
          item.textContent = `${product.item_name} - P${parseFloat(
            product.price_per_item
          ).toFixed(2)}`;
          item.dataset.name = product.item_name;
          item.dataset.price = product.price_per_item;

          // Add click event to fill the form
          item.addEventListener("click", () => {
            inputItemName.value = item.dataset.name;
            inputItemPrice.value = parseFloat(item.dataset.price).toFixed(2);

            // Clear results and focus on quantity
            searchResults.innerHTML = "";
            searchResults.classList.remove("active");
            productSearch.value = "";
            inputItemQty.focus();
          });

          searchResults.appendChild(item);
        });
        searchResults.classList.add("active");
      } else {
        searchResults.innerHTML =
          '<div class="search-result-empty">No products found.</div>';
        searchResults.classList.add("active");
      }
    } catch (error) {
      console.error("Search error:", error);
      searchResults.innerHTML =
        '<div class="search-result-empty">Error searching.</div>';
      searchResults.classList.add("active");
    }
  }

  // Event listener for the search bar
  productSearch.addEventListener("keyup", (e) => {
    clearTimeout(searchTimeout);
    const searchTerm = productSearch.value.trim();

    // Use a timeout to avoid spamming the API on every keystroke
    searchTimeout = setTimeout(() => {
      searchProducts(searchTerm);
    }, 250); // 250ms delay
  });

  // Hide results if clicking elsewhere
  document.addEventListener("click", (e) => {
    if (!searchResults.contains(e.target) && e.target !== productSearch) {
      searchResults.innerHTML = "";
      searchResults.classList.remove("active");
    }
  });
});
