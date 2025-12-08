// This function runs when you click the "X" button
function confirmDelete(id) {
    // 1. Show a popup window asking "Are you sure?"
    // 'confirm' returns TRUE if they click OK, FALSE if they click Cancel.
    let check = confirm("Are you sure you want to remove this visitor?");

    // 2. If they clicked OK (true)
    if (check) {
        // 3. Send them to index.php with the delete command
        window.location.href = "index.php?delete=" + id;
    }
}