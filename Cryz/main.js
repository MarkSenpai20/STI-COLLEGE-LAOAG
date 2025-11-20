document.addEventListener("DOMContentLoaded", () => {
    
    // --- 1. Environment Detection Logic ---
    function detectEnvironment() {
        const body = document.body;
        // Check User Agent or Screen Width (Mobile breakpoint usually < 768px)
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) || window.innerWidth < 768;

        if (isMobile) {
            if (!body.classList.contains('env-mobile')) {
                console.log("Environment: Mobile detected. Adjusting layout...");
                body.classList.add('env-mobile');
                body.classList.remove('env-desktop');
            }
        } else {
            if (!body.classList.contains('env-desktop')) {
                console.log("Environment: Desktop detected. Using simulator view.");
                body.classList.add('env-desktop');
                body.classList.remove('env-mobile');
            }
        }
    }

    // Run on load and on resize
    detectEnvironment();
    window.addEventListener('resize', detectEnvironment);


    // --- 2. Existing App Logic ---
    const btnVoid = document.getElementById("btn-void");
    const btnCancelVoid = document.getElementById("btn-cancel-void");
    const statusMessage = document.getElementById("status-message");
    const itemList = document.getElementById("item-list");
    const itemRows = document.querySelectorAll(".item-row");

    // Disable buttons if they don't exist (e.g., cart is empty)
    if (!btnVoid) return;

    let isVoidMode = false;

    function toggleVoidMode(state) {
        isVoidMode = state;

        if (isVoidMode) {
            statusMessage.textContent = "Select an item to void first";
            btnCancelVoid.classList.remove("hidden");
            itemList.classList.add("void-selectable");
            // Add selectable class to items
            itemRows.forEach(row => row.classList.add("void-selectable"));
            // Disable other buttons
            document.querySelectorAll(".btn").forEach(btn => {
                if (btn.id !== 'btn-void' && btn.id !== 'btn-cancel-void') {
                    btn.classList.add('disabled');
                }
            });

        } else {
            statusMessage.textContent = "";
            btnCancelVoid.classList.add("hidden");
            itemList.classList.remove("void-selectable");
            // Remove selectable class
            itemRows.forEach(row => {
                row.classList.remove("void-selectable");
                row.classList.remove("item-selected"); // Clear selection
            });
            // Re-enable buttons
            document.querySelectorAll(".btn").forEach(btn => {
                btn.classList.remove('disabled');
            });
            // Re-check disabled state based on cart
            const hasItems = itemRows.length > 0 && !itemRows[0].classList.contains('empty');
            if (!hasItems) {
                if(document.getElementById('btn-pay')) document.getElementById('btn-pay').classList.add('disabled');
                if(document.getElementById('btn-void')) document.getElementById('btn-void').classList.add('disabled');
            }
        }
    }

    // --- Event Listeners ---

    btnVoid.addEventListener("click", () => {
        toggleVoidMode(true);
    });

    btnCancelVoid.addEventListener("click", () => {
        toggleVoidMode(false);
    });

    // Add click listener to each item row
    itemRows.forEach(row => {
        row.addEventListener("click", () => {
            if (!isVoidMode || row.classList.contains('empty')) {
                return;
            }
            
            // Highlight selected item
            itemRows.forEach(r => r.classList.remove('item-selected'));
            row.classList.add('item-selected');

            const itemIndex = row.dataset.index;

            // Call the API to void the item
            fetch(`api.php?action=void&id=${itemIndex}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload the page to show the updated list
                        location.reload();
                    } else {
                        statusMessage.textContent = "Error: Could not void item.";
                        toggleVoidMode(false);
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    statusMessage.textContent = "Error: Request failed.";
                    toggleVoidMode(false);
                });
        });
    });
});