document.addEventListener("DOMContentLoaded", () => {
  const displayToPay = document.getElementById("display-to-pay");
  const displayBalance = document.getElementById("display-balance");
  const displayChange = document.getElementById("display-change");
  const inputBalance = document.getElementById("input-balance");
  const inputChange = document.getElementById("input-change");
  const btnClearBalance = document.getElementById("btn-clear-balance");
  const payForm = document.getElementById("pay-form");
  const completePaymentBtn = document.getElementById("btn-complete-payment");

  // Get total amount from data attribute
  const totalToPay = parseFloat(displayToPay.dataset.total);
  let currentBalance = 0.0;

  function formatCurrency(value) {
    return `P${value.toFixed(2)}`;
  }

  function updateDisplay() {
    const change = currentBalance - totalToPay;

    displayBalance.value = formatCurrency(currentBalance);
    displayChange.value = formatCurrency(change);

    // Update hidden inputs for form submission
    inputBalance.value = currentBalance.toFixed(2);
    inputChange.value = change.toFixed(2);

    // Style the change display
    if (change < 0) {
      displayChange.classList.add("negative");
      displayChange.classList.remove("positive");
      completePaymentBtn.classList.add("disabled"); // Disable payment when not enough
      completePaymentBtn.disabled = true;
    } else {
      displayChange.classList.remove("negative");
      displayChange.classList.add("positive");
      completePaymentBtn.classList.remove("disabled");
      completePaymentBtn.disabled = false;
    }
  }

  // Add click listener to all calculator buttons
  document.querySelectorAll(".denomination").forEach((button) => {
    button.addEventListener("click", () => {
      const value = parseFloat(button.dataset.value);
      currentBalance += value;
      updateDisplay();
    });
  });

  // Listener for "Clear Balance"
  btnClearBalance.addEventListener("click", () => {
    currentBalance = 0.0;
    updateDisplay();
  });

  // Prevent submitting the form if payment is insufficient
  payForm.addEventListener("submit", (e) => {
    const change = currentBalance - totalToPay;
    if (change < 0) {
      e.preventDefault(); // Stop form submission
      alert("Balance is insufficient to complete payment.");
    }
  });

  // Initial call to set correct change color
  updateDisplay();
});
