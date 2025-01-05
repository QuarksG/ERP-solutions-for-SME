document.addEventListener('DOMContentLoaded', () => {
    const searchBox = document.getElementById('search-box');
    const vendorTable = document.getElementById('vendor-table');
    const rows = vendorTable.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    // Search functionality
    searchBox.addEventListener('input', () => {
        const searchTerm = searchBox.value.toLowerCase();
        Array.from(rows).forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
            row.style.display = rowText.includes(searchTerm) ? '' : 'none';
        });
    });

    // Download as CSV functionality
    document.getElementById('download-csv').addEventListener('click', () => {
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += Array.from(vendorTable.getElementsByTagName('thead')[0].getElementsByTagName('th'))
            .map(th => th.textContent.trim()).join(",") + "\n";
        csvContent += Array.from(rows)
            .map(row => Array.from(row.getElementsByTagName('td'))
                .map(td => td.textContent.trim()).join(",")).join("\n");

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "vendors.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });

    // Alacakli Listesi filter
    document.getElementById('alacakli-listesi').addEventListener('click', () => {
        Array.from(rows).forEach(row => {
            const zustatek = parseFloat(row.cells[9].textContent.replace(/,/g, ''));
            row.style.display = zustatek > 0 ? '' : 'none';
        });
        highlightButton('alacakli-listesi');
    });

    // Askiya alinanlar filter
    document.getElementById('askiya-alinanlar').addEventListener('click', () => {
        Array.from(rows).forEach(row => {
            const assets = parseFloat(row.cells[7].textContent.replace(/,/g, ''));
            const zustatek = parseFloat(row.cells[9].textContent.replace(/,/g, ''));
            row.style.display = assets < 50000 && zustatek <= 0 ? '' : 'none';
        });
        highlightButton('askiya-alinanlar');
    });

    // Üst Seviye ticaret filter
    document.getElementById('ust-seviye-ticaret').addEventListener('click', () => {
        const filteredRows = Array.from(rows).filter(row => {
            const assets = parseFloat(row.cells[7].textContent.replace(/,/g, ''));
            const zustatek = parseFloat(row.cells[9].textContent.replace(/,/g, ''));
            return zustatek >= 0 && assets > 0;
        });

        const sortedRows = filteredRows.sort((a, b) => {
            const aAssets = parseFloat(a.cells[7].textContent.replace(/,/g, ''));
            const bAssets = parseFloat(b.cells[7].textContent.replace(/,/g, ''));
            return bAssets - aAssets; // Sort by assets in descending order
        });

        // Clear existing rows
        vendorTable.getElementsByTagName('tbody')[0].innerHTML = '';

        // Append sorted rows
        sortedRows.forEach(row => vendorTable.getElementsByTagName('tbody')[0].appendChild(row));

        highlightButton('ust-seviye-ticaret');
    });

    // Function to highlight the active button and reset others
    function highlightButton(buttonId) {
        document.querySelectorAll('.top-buttons .btn').forEach(button => {
            button.style.backgroundColor = '';
            button.style.color = '';
        });

        const activeButton = document.getElementById(buttonId);
        activeButton.style.backgroundColor = 'blue';
        activeButton.style.color = 'white';
    }
});
