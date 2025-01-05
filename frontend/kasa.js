let currentPage = 1;
let itemsPerPage = 10;

function downloadAsExcel() {
    var table = document.querySelector("table");
    var wb = XLSX.utils.table_to_book(table, {sheet: "Výdaje"});
    XLSX.writeFile(wb, "expenses.xlsx");
}

function paginateTable() {
    itemsPerPage = parseInt(document.getElementById("itemsPerPage").value);
    currentPage = 1;
    showPage(currentPage);
}

function showPage(page) {
    const table = document.querySelector("table tbody");
    const rows = table.querySelectorAll("tr");
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / itemsPerPage);

    document.getElementById("pageInfo").textContent = `Page ${page} of ${totalPages}`;

    // Hide all rows
    rows.forEach((row, index) => {
        row.style.display = "none";
    });

    // Show the relevant rows for the current page
    const start = (page - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    for (let i = start; i < end && i < totalRows; i++) {
        rows[i].style.display = "table-row";
    }
}

function prevPage() {
    if (currentPage > 1) {
        currentPage--;
        showPage(currentPage);
    }
}

function nextPage() {
    const table = document.querySelector("table tbody");
    const totalRows = table.querySelectorAll("tr").length;
    const totalPages = Math.ceil(totalRows / itemsPerPage);

    if (currentPage < totalPages) {
        currentPage++;
        showPage(currentPage);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const inputElement = document.querySelector('input[name="costumer"]');
    inputElement.addEventListener('input', () => showSuggestions(inputElement));
    inputElement.addEventListener('focus', () => showSuggestions(inputElement));
    inputElement.addEventListener('click', () => showSuggestions(inputElement)); // Show suggestions when the input is clicked
    paginateTable();
});

function showSuggestions(inputElement) {
    if (!window.customerVendors) {
        console.error("Customer vendor data not loaded.");
        return;
    }

    const userInput = inputElement.value.toLowerCase();
    const suggestions = window.customerVendors.filter(customerVendor => customerVendor.toLowerCase().includes(userInput));
    let dataList = inputElement.nextElementSibling;

    if (!dataList || !dataList.classList.contains('suggestions-list')) {
        dataList = document.createElement('div');
        dataList.className = 'suggestions-list';
        inputElement.parentNode.appendChild(dataList);
    }

    dataList.innerHTML = '';
    if (suggestions.length > 0) {
        suggestions.forEach(suggestion => {
            const option = document.createElement('div');
            option.className = 'suggestion-item';
            option.textContent = suggestion;

            option.addEventListener('click', () => {
                inputElement.value = suggestion;
                dataList.innerHTML = '';  // Clear the suggestions list after selection
            });

            dataList.appendChild(option);
        });
    } else {
        const noResults = document.createElement('div');
        noResults.className = 'suggestion-item';
        noResults.textContent = 'No results found';
        dataList.appendChild(noResults);
    }

    applySuggestionListStyles();
}

function applySuggestionListStyles() {
    if (document.querySelector('style#suggestionStyles')) return;

    const style = document.createElement('style');
    style.id = 'suggestionStyles';
    style.innerHTML = `
        .suggestions-list {
            position: absolute;
            background-color: #333;
            color: white;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ccc;
            z-index: 1000;
        }
        .suggestion-item {
            padding: 10px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background-color: #555;
        }
    `;
    document.head.appendChild(style);
}
