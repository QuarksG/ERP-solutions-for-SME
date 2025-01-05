let currentPage = 1;
let itemsPerPage = 5;

document.addEventListener('DOMContentLoaded', function () {
    // Update the table to show the correct number of items per page
    updateTable();
    
    // Set up event listener for items per page selection
    const itemsPerPageSelect = document.getElementById('itemsPerPage');
    if (itemsPerPageSelect) {
        itemsPerPageSelect.addEventListener('change', function () {
            itemsPerPage = parseInt(this.value);
            currentPage = 1; // Reset to the first page whenever items per page is changed
            updateTable();
        });
    }
});

function changePage(delta) {
    const table = document.querySelector('.styled-table tbody');
    const rows = table.getElementsByTagName('tr');
    const totalPages = Math.ceil(rows.length / itemsPerPage);

    currentPage += delta;

    if (currentPage < 1) currentPage = 1;
    if (currentPage > totalPages) currentPage = totalPages;

    document.getElementById('pageNumber').textContent = `Page ${currentPage}`;
    updateTable();
}

function updateTable() {
    const table = document.querySelector('.styled-table tbody');
    const rows = table.getElementsByTagName('tr');
    const totalPages = Math.ceil(rows.length / itemsPerPage);

    for (let i = 0; i < rows.length; i++) {
        rows[i].style.display = (i >= (currentPage - 1) * itemsPerPage && i < currentPage * itemsPerPage) ? '' : 'none';
    }

    document.getElementById('pageNumber').textContent = `Page ${currentPage} of ${totalPages}`;
}

function filterTable() {
    let input = document.getElementById('searchBox');
    let filter = input.value.toUpperCase();
    let table = document.querySelector('.styled-table tbody');
    let rows = table.getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        let cells = rows[i].getElementsByTagName('td');
        let match = false;

        for (let j = 0; j < cells.length; j++) {
            let cell = cells[j];
            if (cell) {
                if (cell.textContent.toUpperCase().indexOf(filter) > -1) {
                    match = true;
                    break;
                }
            }
        }

        rows[i].style.display = match ? '' : 'none';
    }
    
    // Reset pagination after filtering
    currentPage = 1;
    updateTable();
}

// Enable inline editing of table rows
function enableInlineEdit(button, rowId) {
    const row = document.getElementById(`row-${rowId}`);
    const cells = row.getElementsByTagName('td');

    for (let i = 1; i < cells.length - 2; i++) { // Skip the first and last two columns (ID, Vendor Code, Actions)
        let cell = cells[i];
        let inputValue = cell.textContent.trim();
        if (i === cells.length - 3) { // This is the note field, handle textarea
            cell.innerHTML = `<textarea name="edit-${rowId}-${i}" rows="2">${inputValue}</textarea>`;
        } else {
            cell.innerHTML = `<input type="text" name="edit-${rowId}-${i}" value="${inputValue}" />`;
        }
    }

    button.textContent = 'Uložit';
    button.setAttribute('onclick', `saveInlineEdit(${rowId})`);
}

function saveInlineEdit(rowId) {
    const form = document.createElement('form');
    form.method = 'POST';

    // Add hidden field to indicate this is an inline edit submission
    const hiddenField = document.createElement('input');
    hiddenField.type = 'hidden';
    hiddenField.name = 'inline_edit';
    hiddenField.value = rowId;
    form.appendChild(hiddenField);

    // Gather all the input fields created for editing
    const row = document.getElementById(`row-${rowId}`);
    const cells = row.getElementsByTagName('td');

    for (let i = 1; i < cells.length - 2; i++) {
        let inputElement = cells[i].querySelector('input, textarea');
        if (inputElement) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `edit-${rowId}-${i}`;
            hiddenInput.value = inputElement.value;
            form.appendChild(hiddenInput);
        }
    }

    document.body.appendChild(form);
    form.submit();
}

function confirmDeletion() {
    return confirm('Are you sure you want to delete this customer/vendor?');
}
