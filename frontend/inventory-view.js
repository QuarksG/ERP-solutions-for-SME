document.addEventListener('DOMContentLoaded', function () {
    // Function to show a toast notification
    function showToast(message, type) {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');

        toastMessage.textContent = message;
        toast.style.backgroundColor = (type === 'success') ? '#28a745' : '#dc3545';

        toast.className = 'toast show';

        // Hide the toast after 3 seconds
        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 3000);
    }

    // Function to handle editing a product
    window.editProduct = function (id) {
        const row = document.getElementById('row-' + id);
        const cells = row.querySelectorAll('td:not(:last-child)');

        cells.forEach(cell => {
            cell.contentEditable = true;
            cell.classList.add('editable');
        });

        const editButton = row.querySelector('.edit-button');
        editButton.textContent = 'Save';
        editButton.onclick = function () { saveProduct(id); };
    }

    // Function to save an edited product
    window.saveProduct = function (id) {
        const row = document.getElementById('row-' + id);
        const cells = row.querySelectorAll('td:not(:last-child)');

        const data = {
            edit_id: id,
            name: cells[1].textContent.trim(),
            price: cells[2].textContent.trim(),
            type: cells[3].textContent.trim(),
            quantity: cells[4].textContent.trim(),
            cenak: cells[5].textContent.trim()
        };

        fetch('/SideBar/BackEnd/Inventory-view/update_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(responseData => {
            console.log('Server response:', responseData); // Log the response for debugging

            if (responseData.success) {
                cells.forEach(cell => {
                    cell.contentEditable = false;
                    cell.classList.remove('editable');
                });

                showToast('Product updated successfully!', 'success');

                const editButton = row.querySelector('.edit-button');
                editButton.textContent = 'Edit';
                editButton.onclick = function () { editProduct(id); };
            } else {
                showToast('Failed to update product: ' + responseData.message, 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error); // Log any fetch errors
            showToast('Failed to update product: ' + error.message, 'error');
        });
    }

    // Function to confirm product deletion
    window.confirmDelete = function (id) {
        const modal = document.getElementById('deleteModal');
        const deleteIdInput = document.getElementById('delete_id');
        deleteIdInput.value = id;
        modal.style.display = 'block';
    }

    // Function to close the delete confirmation modal
    window.closeModal = function () {
        const modal = document.getElementById('deleteModal');
        modal.style.display = 'none';
    }

    // Function to change the number of items per page
    function changeItemsPerPage() {
        const itemsPerPage = parseInt(document.querySelector('#itemsPerPage').value);
        const rows = document.querySelectorAll('table tbody tr');

        // Hide all rows first
        rows.forEach(row => {
            row.style.display = 'none';
        });

        // Show only the first 'itemsPerPage' rows
        rows.forEach((row, index) => {
            if (index < itemsPerPage) {
                row.style.display = '';
            }
        });

        // Optionally, add logic here to handle pagination if needed
    }

    // Attach event listener to the "Items Per Page" dropdown
    document.querySelector('#itemsPerPage').addEventListener('change', changeItemsPerPage, false);

    // Function to download CSV
    function downloadCSV(csv, filename) {
        const BOM = "\uFEFF"; 
        const csvFile = new Blob([BOM + csv], {type: "text/csv;charset=utf-8;"});
        const downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    // Function to export the table to CSV
    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll("table tr");

        for (const row of rows) {
            const rowData = Array.from(row.querySelectorAll("th, td:not(:last-child)")) 
                .map(cell => `"${cell.textContent.replace(/"/g, '""')}"`);
            csv.push(rowData.join(","));
        }

        downloadCSV(csv.join("\n"), filename);
    }

    // Attach event listener to the "Download Excel" button
    document.querySelector('#downloadExcel').addEventListener('click', function () {
        const filename = prompt("Enter filename for the CSV", "inventory_data.csv") || "inventory_data.csv";
        exportTableToCSV(filename);
    }, false);

    // Function to handle search functionality
    document.querySelector('#searchInput').addEventListener('input', function () {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td:not(:last-child)');
            const matches = Array.from(cells).some(cell => cell.textContent.toLowerCase().includes(searchTerm));
            row.style.display = matches ? '' : 'none';
        });
    });

    // Initial call to display items based on the default items per page
    changeItemsPerPage();
});
