document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.querySelector('.styled-table');

    // Export to Excel
    document.querySelector('.btn-export-excel').addEventListener('click', function () {
        exportTableToExcel(tableElement, 'delivery_notes');
    });

    // Export to PDF (using autoTable for the whole table)
    document.querySelector('.btn-export-pdf').addEventListener('click', function () {
        generatePDF(tableElement);
    });

    // Search Table by Driver Name
    document.getElementById('searchBox').addEventListener('keyup', function () {
        searchTableByDriverName(tableElement, this.value);
    });

    // Generate Custom Kafa Kagdi PDF
    document.getElementById('generatePdfButton').addEventListener('click', function () {
        generateKafaKagdiPDF();
    });
});





function generateKafaKagdiPDF() {
    // Get table data
    const tableElement = document.querySelector('.styled-table tbody');
    const rows = Array.from(tableElement.querySelectorAll('tr')).filter(row => {
        return row.style.display !== 'none';  // Filter only visible rows
    });

    // Extract the RIDIC name from the filtered rows
    const ridicName = rows.length > 0 ? rows[0].querySelectorAll('td')[7]?.textContent.trim() || "Unknown_RIDIC" : "Unknown_RIDIC"; // Adjust the index as needed

    // Create a map to group items by orderNumber
    const ordersMap = new Map();
    const consolidatedItemsMap = new Map(); // Map to consolidate all items

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const orderNumber = cells[1]?.textContent.trim() || "";
        const deliveryAddress = cells[2]?.textContent.trim() || "";
        const itemName = cells[8]?.textContent.trim() || "";
        const quantity = cells[9]?.textContent.trim() || "0";
        const typ = cells[10]?.textContent.trim() || "";

        if (!ordersMap.has(orderNumber)) {
            ordersMap.set(orderNumber, {
                deliveryAddress: deliveryAddress,
                items: []
            });
        }

        // Add item to the corresponding orderNumber group
        ordersMap.get(orderNumber).items.push({
            itemName: itemName,
            quantity: parseInt(quantity, 10),  // Parse quantity as an integer for sorting
            typ: typ
        });

        // Consolidate the items regardless of order
        const consolidatedKey = `${typ} - ${itemName}`;
        if (!consolidatedItemsMap.has(consolidatedKey)) {
            consolidatedItemsMap.set(consolidatedKey, 0);
        }
        consolidatedItemsMap.set(consolidatedKey, consolidatedItemsMap.get(consolidatedKey) + parseInt(quantity, 10));
    });

    // Sort items within each order by Typ, then by Item Name, then by Quantity in ascending order
    ordersMap.forEach(order => {
        order.items.sort((a, b) => {
            const typeA = a.typ || "";
            const typeB = b.typ || "";
            const nameA = a.itemName || "";
            const nameB = b.itemName || "";
            const quantityA = a.quantity || 0;
            const quantityB = b.quantity || 0;

            return typeA.localeCompare(typeB) || nameA.localeCompare(nameB) || quantityA - quantityB;
        });
    });

    // Sort consolidated items alphabetically by type
    const sortedConsolidatedItems = Array.from(consolidatedItemsMap.entries()).sort((a, b) => {
        const [keyA] = a;
        const [keyB] = b;
        return keyA.localeCompare(keyB);
    });

    // Initialize jsPDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'p',
        unit: 'mm',
        format: [290, 350]
    });

    // Set font to a monospaced font for consistent alignment
    doc.setFont('courier', 'normal');
    doc.setFontSize(9);  // Adjusted font size for consistent output

    const ordersPerPage = 10;
    let yPosition = 20;
    let columnPosition = 10;
    let columnWidth = 85;
    let orderCount = 0;

    // Add "RIDIC: {RIDIC Name}" at the top of the first page
    doc.setFont('courier', 'bold');
    doc.setFontSize(9);  // Larger font size for RIDIC
    doc.text(`RIDIC: ${ridicName}`, 10, 10);
    doc.setFontSize(11);  // Reset font size for the rest of the document

    ordersMap.forEach((order, orderNumber) => {
        if (orderCount > 0 && orderCount % ordersPerPage === 0) {
            doc.addPage();
            yPosition = 20;
            columnPosition = 10;

            // Add "RIDIC: {RIDIC Name}" at the top of every new page
            doc.setFont('courier', 'bold');
            doc.setFontSize(12);
            doc.text(`RIDIC: ${ridicName}`, 10, 10);
            doc.setFontSize(8);  // Reset font size for the rest of the document
        }

        if (orderCount % 5 === 0 && orderCount % ordersPerPage !== 0) {
            columnPosition += columnWidth;
            yPosition = 20;
        }

        // Add order number and address with custom text size and color
        doc.setFont('courier', 'bold');
        doc.setFontSize(8);  // Set font size for the order number and address
        doc.setTextColor(0, 0, 128);  // Set text color to a dark blue
        doc.text(orderNumber, columnPosition, yPosition);
        yPosition += 4;
        doc.setFont('courier', 'normal');
        doc.text(order.deliveryAddress, columnPosition, yPosition);
        yPosition += 4;
        
        // Add a separator line
        doc.setDrawColor(0, 0, 0);
        doc.line(columnPosition, yPosition, columnPosition + columnWidth - 10, yPosition);
        yPosition += 4;

        // Set font size and color for items
        doc.setFontSize(9);  // Smaller font size for item details
        doc.setTextColor(0, 0, 0);  // Reset text color to black

        // Add items under the order
        order.items.forEach(item => {
            const itemDetails = `${item.typ} - ${item.itemName}`;  // Combine type and name
            const itemQuantity = `${item.quantity}`.padStart(4);   // Right-align the quantity
            const quantityPosition = columnPosition + 70;         // Fixed position for quantity
    
            // Ensure the item name doesn't wrap by removing the maxWidth parameter
            doc.text(itemDetails, columnPosition, yPosition);
            
            // Position the quantity at a fixed position to the right
            doc.text(itemQuantity, quantityPosition, yPosition);
            
            yPosition += 4;  // Move to the next line
        });

        yPosition += 8; // Space between orders
        orderCount++;
    });

    // Set up the right side and determine the ending point of the separator line
    let rightYPosition = 20;

    // Add vertical separator line with a light thickness
    const lineX = 200;  // Adjusted position for better alignment
    doc.setDrawColor(0, 0, 0);
    doc.setLineWidth(0.2);  // Very light line thickness
    const separatorStartY = rightYPosition;
    
    // Iterate over the sorted consolidated items and print them
    sortedConsolidatedItems.forEach(([item, quantity]) => {
        const itemDetails = `${item}`;
        const quantityPosition = lineX + 75; // Adjusted position for quantity alignment
        doc.text(itemDetails, lineX + 5, rightYPosition);  // Move the text closer to the line
        doc.text(`${quantity}`, quantityPosition, rightYPosition);  // Right-align quantity
        rightYPosition += 4;  // Move to the next line
    });

    // Draw the vertical separator line, ending where the items end
    doc.line(lineX, separatorStartY, lineX, rightYPosition - 4); // Draw line from top to just below the last item

    // Save the PDF with the RIDIC name in the filename
    const filename = `kafa_kagdi_${ridicName.replace(/\s+/g, '_')}.pdf`;
    doc.save(filename);
}






function getUniqueTableCellValues(selector) {
    const values = new Set();
    document.querySelectorAll(selector).forEach(cell => {
        const text = cell.textContent.trim();
        if (text) {
            values.add(text);
        }
    });
    return Array.from(values).join(', ');
}

function exportTableToExcel(table, filename = '') {
    if (!table) {
        return;
    }

    let tableHTML = '';
    let rows = table.querySelectorAll('tr');

    rows.forEach(row => {
        let rowHTML = '';
        let cells = row.querySelectorAll('th, td');

        cells.forEach(cell => {
            rowHTML += '"' + cell.innerText.replace(/"/g, '""') + '"\t';
        });

        tableHTML += rowHTML.trimEnd() + '\n';
    });

    let utf8Bom = '\ufeff';
    let blob = new Blob([utf8Bom + tableHTML], { type: 'application/vnd.ms-excel;charset=utf-8;' });
    let url = URL.createObjectURL(blob);

    let downloadLink = document.createElement('a');
    downloadLink.href = url;
    downloadLink.download = filename ? filename + '.xls' : 'excel_data.xls';

    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
    URL.revokeObjectURL(url);
}

function searchTableByDriverName(table, searchTerm) {
    if (!table) {
        return;
    }

    const driverNameColumnIndex = Array.from(table.querySelectorAll('thead th'))
        .findIndex(th => th.innerText.trim() === 'Ridiče');

    if (driverNameColumnIndex === -1) {
        return;
    }

    let filter = searchTerm.toLowerCase();
    let rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        let driverNameCell = rows[i].getElementsByTagName('td')[driverNameColumnIndex];
        if (driverNameCell) {
            let txtValue = driverNameCell.textContent || driverNameCell.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}


function generatePDF(table) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'p',
        unit: 'mm',
        format: 'a4'
    });

    // Get visible rows only
    const visibleRows = Array.from(table.querySelectorAll('tbody tr')).filter(row => {
        return row.style.display !== 'none';
    });

    // Gather unique values for Ridiče and deliveryAddress based on visible rows only
    const uniqueRidičeValues = getUniqueTableCellValues(visibleRows, 7); // Adjust the index as needed
    const uniqueDeliveryAddressValues = getUniqueTableCellValues(visibleRows, 2); // Adjust the index as needed

    const leftSideDetails = [
        { label: 'Ridice', value: uniqueRidičeValues },
        { label: 'Delivery Address', value: uniqueDeliveryAddressValues }
    ];

    // Generate table with Ridiče and Delivery Address
    doc.autoTable({
        body: leftSideDetails.map(detail => [detail.label, detail.value]),
        startY: 10,
        startX: 10,
        styles: { fontSize: 10, cellPadding: 3, overflow: 'linebreak' },
        theme: 'grid'
    });

    // Collecting product details sorted by Typ (product type) in ascending order
    let productDetails = [];

    visibleRows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const itemName = cells[8]?.textContent.trim() || ""; // Adjust the index as needed
        const quantityText = cells[9]?.textContent.trim() || "0"; // Adjust the index as needed
        const quantity = parseInt(quantityText, 10);
        const productType = cells[10]?.textContent.trim() || ""; // Adjust the index as needed

        productDetails.push({
            name: itemName,
            quantity: quantity,
            type: productType
        });
    });

    // Sort by Typ (product type), then by Item Name, and then by Quantity in ascending order
    productDetails.sort((a, b) => {
        const typeA = a.type || "";
        const typeB = b.type || "";
        const nameA = a.name || "";
        const nameB = b.name || "";
        const quantityA = a.quantity || 0;
        const quantityB = b.quantity || 0;

        return typeA.localeCompare(typeB) || nameA.localeCompare(nameB) || quantityA - quantityB;
    });

    const sortedTableData = productDetails.map(({ name, quantity, type }) => [name, quantity.toString(), type]);

    let productsStartY = doc.lastAutoTable.finalY + 10;

    doc.autoTable({
        head: [['Item Name', 'Quantity', 'Product Type']],
        body: sortedTableData,
        startY: productsStartY,
        theme: 'striped',
        columnStyles: { 0: { cellWidth: 'auto' }, 1: { cellWidth: 'auto' }, 2: { cellWidth: 'auto' } }
    });

    const totalQuantity = sortedTableData.reduce((sum, row) => sum + parseInt(row[1], 10), 0);
    doc.text(`Total Quantity: ${totalQuantity}`, 10, doc.lastAutoTable.finalY + 10);

    // Create the PDF filename based on Ridiče
    const ridicName = uniqueRidičeValues.split(', ')[0]; // Use the first unique name found
    const filename = `Kafa_Kagdi_${ridicName}.pdf`;
    doc.save(filename);
}



function getUniqueTableCellValues(rows, cellIndex) {
    const values = new Set();
    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const text = cells[cellIndex]?.textContent.trim();
        if (text) {
            values.add(text);
        }
    });
    return Array.from(values).join(', ');
}
