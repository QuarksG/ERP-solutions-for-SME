function fetchAndGeneratePDF(deliveryNoteNumber) {
    if (!deliveryNoteNumber) {
        alert('Invalid delivery note number.');
        return;
    }

    fetch(`SideBar/BackEnd/order-view/pdf.php?deliveryNoteNumber=${encodeURIComponent(deliveryNoteNumber)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Data received from server:', data);

            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }

            // Ensure that the necessary fields exist before passing to generatePDF
            if (Array.isArray(data) && data.length > 0 && data[0].orderNumber) {
                generatePDF(data); // Pass the entire array to generatePDF
            } else {
                alert('No valid data found.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while fetching the data.');
        });
}


function generatePDF(dataArray) {
    try {
        const pdfWidthInPoints = 1300;
        const pointsToMM = 0.352778;
        const pdfWidthInMM = pdfWidthInPoints * pointsToMM;

        const doc = new jspdf.jsPDF({
            orientation: 'p',
            unit: 'mm',
            format: [pdfWidthInMM, 297]
        });

        // Set default font
        if (doc.getFontList().CustomFont) {
            doc.setFont("CustomFont");
        } else {
            doc.setFont("helvetica");
        }

        // Title
        doc.setFontSize(20);
        doc.text("Dodací List", 10, 20);

        // Assuming all items share the same left and right side details
        const firstItem = dataArray[0];

        // Left side details
        const leftSideDetails = [
            { label: 'Odberatel', value: firstItem.orderNumber },
            { label: 'Dodací Adresa', value: firstItem.deliveryAddress || 'N/A' },
            { label: 'ICO', value: firstItem.icoNumber || 'N/A' },
            { label: 'DIC', value: firstItem.dicNumber || 'N/A' },
            { label: 'Název', value: firstItem.companyName || 'N/A' }
        ];

        // Right side details
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}.${month}.${year}`;
        }

        const rightSideDetails = [
            { label: 'Datum', value: formatDate(firstItem.orderDate) },
            { label: 'Dodací Líst Císlo', value: firstItem.deliveryNoteNumber || 'N/A' },
            { label: 'Ridic', value: firstItem.driverName || 'N/A' }
        ];

        // Convert details to table data
        function detailsToTableData(details) {
            return details.map(detail => [detail.label, detail.value]);
        }

        // Options for the left and right tables
        const tableOptions = {
            margin: { top: 5 },
            styles: { fontSize: 15, cellPadding: 3, overflow: 'linebreak' },
            bodyStyles: { fillColor: [255, 255, 255], textColor: [0, 0, 0], valign: 'middle' },
            columnStyles: { 0: { cellWidth: 60 }, 1: { cellWidth: 50 } },
            theme: 'grid'
        };

        // Left-side table
        doc.autoTable({
            body: detailsToTableData(leftSideDetails),
            startY: 40,
            startX: 10,
            ...tableOptions
        });

        // Right-side table
        if (rightSideDetails.length > 0) {
            const horizontalPadding = 2;
            const verticalPadding = 2;
            const lineColor = [225, 225, 225];
            const lineWidth = 0.5;

            let rightSideStartY = doc.lastAutoTable.finalY -56;
            const rightSideStartX = 170;
            const columnWidth = 46;
            const valueWidth = 65;
            const rightTableWidth = columnWidth + valueWidth;
            const lineHeight = 18;

            doc.setFontSize(15);
            doc.setDrawColor(...lineColor);
            doc.setLineWidth(lineWidth);

            rightSideDetails.forEach((detail, index) => {
                let currentY = rightSideStartY + (lineHeight * index);

                doc.setFillColor(255, 255, 255);
                doc.rect(rightSideStartX, currentY, columnWidth, lineHeight, 'FD');
                doc.rect(rightSideStartX + columnWidth, currentY, valueWidth, lineHeight, 'FD');

                doc.text(detail.label + ":", rightSideStartX + horizontalPadding, currentY + lineHeight / 2 + verticalPadding);
                doc.text(detail.value, rightSideStartX + columnWidth + horizontalPadding, currentY + lineHeight / 2 + verticalPadding);
            });

            let bottomY = rightSideStartY + (lineHeight * rightSideDetails.length);
            doc.rect(rightSideStartX, rightSideStartY, rightTableWidth, bottomY - rightSideStartY, 'S');
        } else {
            console.warn("No right side details to add to the table");
        }

        // Order items table
        let orderItems = dataArray.map(item => [
            item.itemName || '',
            item.quantity || '',
            item.unitPrice || '',
            item.totalItemAmount || ''
        ]);

        if (orderItems.length > 0) {
            let orderItemsStartY = doc.lastAutoTable.finalY +15;

            doc.setFontSize(20);
            doc.text("Položky objednávky", 10, orderItemsStartY);

            orderItemsStartY += 8;

            doc.autoTable({
                head: [['Oznacení položky', 'Množství', 'Jedn.Cena', 'Celkem']],
                body: orderItems,
                startY: orderItemsStartY,
                styles: { cellPadding: 1, overflow: 'linebreak', fontSize: 12 },
                headStyles: { fontSize: 15, fillColor: [200, 200, 200], textColor: [0, 0, 0], halign: 'left' },
                columnStyles: { 0: { cellWidth: 'auto' }, 1: { cellWidth: 'auto' }, 2: { cellWidth: 'auto' }, 3: { cellWidth: 'auto' } },
                theme: 'grid'
            });
        } else {
            console.warn("No order items to add to the table");
        }

        // Financial details table
        const pdfWidth = 210; // A4 width in mm
        const tableWidth = 70 + 50; // Width of the table (column 0 width + column 1 width)
        const startX = pdfWidth - tableWidth +100; // 10mm padding from the right edge
        
        // Define the financial details
        let financialDetails = [
            ['Celkem', firstItem.totalServiceAmount || 'N/A'],
            ['Stary Zustatek', firstItem.oldBalance || 'N/A'],
            ['Castka k Uhrade', firstItem.amountDue || 'N/A']
        ];
        
        let financialDetailsStartY = doc.lastAutoTable.finalY +25; // Adjust this based on your layout
        
        
        // Position the table at the far right
        
        doc.autoTable({
            body: financialDetails,
            startY: financialDetailsStartY, // Set the vertical position
            startX: startX, // Set the horizontal position to the far right
            margin: { top: 200, left: 200, right: 10 }, // Adjust the margins if needed
            styles: { 
                fontSize: 11, 
                cellPadding: 3, 
                rowHeight: 10, // Adjust row height
                overflow: 'linebreak',
                halign: 'left', // Align the text horizontally to the left
                valign: 'middle',  // Align the text vertically to the middle
                fillColor: [224, 224, 224], // Default background color for all cells
            }, 
            columnStyles: { 
                0: { 
                    cellWidth: 40, 
                    fillColor: [200, 200, 200] // Darker background for the first column
                }, 
                1: { 
                    cellWidth: 40, 
                    fillColor: [230, 230, 230] // Lighter background for the second column
                }   
            },
            theme: 'grid'
        });


                        
        
                const deliveryNoteNumber = firstItem.deliveryNoteNumber || 'unknown';
        const filename = "Dodací_list_číslo_" + deliveryNoteNumber + ".pdf";

        doc.save(filename);
        console.log("PDF saved as", filename);
    } catch (error) {
        console.error("An error occurred while generating the PDF:", error);
        alert("There was an error generating the PDF. Please check the console for more details.");
    }
}


document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('searchBox');
    searchBox.addEventListener('input', function() {
        const searchText = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#orderTableBody tr');

        tableRows.forEach(row => {
            const textContent = row.textContent.toLowerCase();
            const isMatch = searchText.split(' ').every((val) => textContent.includes(val));
            row.style.display = isMatch ? '' : 'none';
        });
    });
});








function setupExcelExportButton() {
    const downloadButton = document.getElementById('downloadExcel');
    downloadButton.addEventListener('click', function() {
        exportTableToExcel('downloadRecords', 'data-export');
    });
}






function exportTableToExcel(tableID, filename = '') {
    const dataType = 'application/vnd.ms-excel';
    const tableSelect = document.getElementById(tableID);
    let tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');

    filename = filename ? filename + '.xls' : 'excel_data.xls';
    const downloadLink = document.createElement("a");

    document.body.appendChild(downloadLink);

    if (navigator.msSaveOrOpenBlob) {
        const blob = new Blob(['\ufeff', tableHTML], { type: dataType });
        navigator.msSaveOrOpenBlob(blob, filename);
    } else {
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
        downloadLink.download = filename;
        downloadLink.click();
    }
    document.body.removeChild(downloadLink);
}

















