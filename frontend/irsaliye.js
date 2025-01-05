document.addEventListener('DOMContentLoaded', () => {
    console.log("Document is ready");

    setupPriceListButtons();
    attachEventListenersToExistingRows();
    fetchProductList('');
    fetchDriverNames();
    fetchVendors();
    resetUserInputs();

    const recordButton = document.getElementById('recordButton');
    if (recordButton) {
        recordButton.addEventListener('click', handleFormSubmission);
    } else {
        console.error("Record button not found in the DOM.");
    }
});




function handleFormSubmission(event) {
    event.preventDefault(); // Prevent default form submission

    // Gather values from the form
    const orderNumber = document.getElementById('orderNumber')?.value || '';
    const deliveryAddress = document.getElementById('deliveryAddress')?.value || '';
    const icoNumber = document.getElementById('icoNumber')?.value || '';
    const dicNumber = document.getElementById('dicNumber')?.value || '';
    const companyName = document.getElementById('companyName')?.value || '';
    const shippingStart = document.getElementById('shippingStart')?.value || '';
    const orderDate = document.getElementById('orderDate')?.value || '';
    const driverName = document.getElementById('driverName')?.value || '';
    const shippingEnd = document.getElementById('shippingEnd')?.value || '';
    const totalServiceAmount = document.querySelector('.right-side-table [name="totalServiceAmount"]')?.value || '';
    const oldBalance = document.querySelector('.right-side-table [name="oldBalance"]')?.value || '';
    const amountDue = document.querySelector('.right-side-table [name="amountDue"]')?.value || '';
    let deliveryNoteNumber = document.getElementById('deliveryNoteNumber')?.value || '';

    // Gather arrays for items
    const itemNames = Array.from(document.querySelectorAll('input[name="itemName[]"]')).map(input => input.value);
    const quantities = Array.from(document.querySelectorAll('input[name="quantity[]"]')).map(input => input.value);
    const unitPrices = Array.from(document.querySelectorAll('input[name="unitPrice[]"]')).map(input => input.value);
    const totalItemAmounts = Array.from(document.querySelectorAll('input[name="totalItemAmount[]"]')).map(input => input.value);

    // If the delivery note number is not generated yet, request it
    if (!deliveryNoteNumber) {
        fetch('SideBar/BackEnd/order-view/nextdeliverynote.php')
            .then(response => response.json())
            .then(data => {
                if (data && data.deliveryNoteNumber) {
                    deliveryNoteNumber = data.deliveryNoteNumber;
                    document.getElementById('deliveryNoteNumber').value = deliveryNoteNumber;
                    submitFormWithData(deliveryNoteNumber, {
                        orderNumber, 
                        deliveryAddress, 
                        icoNumber, 
                        dicNumber, 
                        companyName, 
                        shippingStart, 
                        orderDate, 
                        driverName, 
                        shippingEnd, 
                        totalServiceAmount, 
                        oldBalance, 
                        amountDue, 
                        itemNames,
                        quantities,
                        unitPrices,
                        totalItemAmounts
                    });
                } else {
                    console.error('No delivery note number returned from server.');
                    throw new Error('Delivery note number generation failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    } else {
        // If the delivery note number is already present, proceed with form submission
        submitFormWithData(deliveryNoteNumber, {
            orderNumber, 
            deliveryAddress, 
            icoNumber, 
            dicNumber, 
            companyName, 
            shippingStart, 
            orderDate, 
            driverName, 
            shippingEnd, 
            totalServiceAmount, 
            oldBalance, 
            amountDue, 
            itemNames,
            quantities,
            unitPrices,
            totalItemAmounts
        });
    }
}

function submitFormWithData(deliveryNoteNumber, data) {
    data.deliveryNoteNumber = deliveryNoteNumber;

    // Now, submit the form data to the server
    fetch('SideBar/BackEnd/order-view/record.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        console.log('Form submission response:', data);
        generatePDF();
    })
    .catch(error => {
        console.error('Error submitting form data:', error);
    });
}





// Function to set up event listeners for price list buttons


// Function to fetch the product list based on the selected price list
function fetchProductList(cenak) {
    console.log("Fetching product list for:", cenak);
    fetch(`SideBar/BackEnd/assets/getInventory.php?cenak=${cenak}`)
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => Promise.reject(new Error(text)));
            }
            return response.json();  // Parse the JSON response
        })
        .then(data => {
            console.log("Raw data received:", JSON.stringify(data, null, 2));  // Log the raw data

            if (data.error) {
                throw new Error(data.error);
            }

            // Initialize itemPrices object
            window.itemPrices = {};

            data.forEach(item => {
                // Ensure the product name is initialized in window.itemPrices
                if (!window.itemPrices[item.Název]) {
                    window.itemPrices[item.Název] = {};
                }

                // Map the price to either cenaA or cenaB based on the Typ
                if (item['Cena K'] === 'A') {
                    window.itemPrices[item.Název].cenaA = parseFloat(item.Cena);
                } else if (item['Cena K'] === 'B') {
                    window.itemPrices[item.Název].cenaB = parseFloat(item.Cena);
                }

                // You can extend this logic for other types like 'F', 'C', etc.
            });

            console.log("Processed product data:", JSON.stringify(window.itemPrices, null, 2));  // Log the processed data
        })
        .catch(error => {
            console.error('Error fetching product list:', error);
        });
}



function setupPriceListButtons() {
    const priceListButtons = ['A', 'B'];

    priceListButtons.forEach(cenak => {
        document.getElementById(`priceList${cenak}`).addEventListener('click', () => {
            highlightButton(cenak);
            fetchProductList(cenak);
        });
    });
}


// Function to highlight the selected price list button
function highlightButton(selectedCenak) {
    const buttons = ['A', 'B'].reduce((acc, key) => {
        acc[key] = document.getElementById(`priceList${key}`);
        return acc;
    }, {});

    Object.values(buttons).forEach(button => {
        button.classList.remove('selected');
        button.style.backgroundColor = '';
        button.style.color = '';
    });

    if (buttons[selectedCenak]) {
        buttons[selectedCenak].classList.add('selected');
        buttons[selectedCenak].style.backgroundColor = 'red';
        buttons[selectedCenak].style.color = 'white';
    }

    window.selectedCenak = selectedCenak;
}



// Function to attach event listeners to existing rows in the table
function attachEventListenersToExistingRows() {
    const rows = document.querySelectorAll('#orderTable tbody tr');
    rows.forEach(attachEventListenersToRow);
}

// Function to add a new row to the order table
function addRow() {
    const table = document.getElementById("orderTable");
    const row = table.insertRow(-1);

    row.innerHTML = `
        <td><input type="text" name="itemName[]" placeholder="Označení položky" oninput="showSuggestions(this)" onchange="updatePriceAndTotal(this)"></td>
        <td><input type="number" name="quantity[]" placeholder="Množství" oninput="calculateTotal(this)"></td>
        <td><input type="number" name="unitPrice[]" placeholder="Jedn.Cena" oninput="calculateTotal(this)"></td>
        <td><input type="text" name="totalItemAmount[]" placeholder="Celkem" readonly></td>
        <td><button type="button" onclick="removeRow(this)">-</button></td>
    `;

    attachEventListenersToRow(row);
}

// Function to attach event listeners to a specific row
function attachEventListenersToRow(row) {
    const itemNameInput = row.querySelector('[name="itemName[]"]');
    const unitPriceInput = row.querySelector('[name="unitPrice[]"]');
    const quantityInput = row.querySelector('[name="quantity[]"]');

    itemNameInput.addEventListener('input', () => showSuggestions(itemNameInput));
    itemNameInput.addEventListener('change', () => {
        updatePriceAndTotal(itemNameInput);
    });

    unitPriceInput.addEventListener('input', () => {
        calculateTotal(unitPriceInput);
    });

    quantityInput.addEventListener('input', () => {
        calculateTotal(quantityInput);
    });
}

// Function to remove a row from the order table
function removeRow(button) {
    const row = button.closest('tr');
    row.remove();
    calculateGrandTotal();
    calculateAmountDue();
}

// Function to show product suggestions as the user types
function showSuggestions(inputElement) {
    if (!window.itemPrices) {
        console.error("Item prices not loaded.");
        return;
    }

    const userInput = inputElement.value.toLowerCase();
    const suggestions = Object.keys(window.itemPrices).filter(itemName => itemName.toLowerCase().includes(userInput));
    let dataList = inputElement.nextElementSibling;

    if (!dataList) {
        dataList = document.createElement('div');
        dataList.className = 'suggestions-list';
        inputElement.parentNode.appendChild(dataList);
    }

    dataList.innerHTML = '';
    suggestions.forEach(suggestion => {
        const option = document.createElement('div');
        option.className = 'suggestion-item';
        option.textContent = suggestion;
        option.style.color = window.selectedCenak ? 'red' : '';

        option.addEventListener('click', () => {
            inputElement.value = suggestion;
            updatePriceAndTotal(inputElement);
            dataList.innerHTML = '';
        });

        dataList.appendChild(option);
    });

    applySuggestionListStyles();
}

// Function to apply custom styles to the suggestions list
function applySuggestionListStyles() {
    if (document.querySelector('style#suggestionStyles')) return;

    const style = document.createElement('style');
    style.id = 'suggestionStyles';
    style.innerHTML = `
        .suggestions-list {
            position: absolute;
            background-color: #333;
            color: white;
            width: calc(40% - 2px);
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

// Function to calculate the total for an item
function calculateTotal(inputElement) {
    const row = inputElement.closest('tr');
    const quantity = parseFloat(row.querySelector('[name="quantity[]"]').value) || 0;
    const unitPrice = parseFloat(row.querySelector('[name="unitPrice[]"]').value) || 0;
    const total = quantity * unitPrice;

    row.querySelector('[name="totalItemAmount[]"]').value = total.toFixed(0);
    calculateGrandTotal();
    calculateAmountDue();
}

// Function to calculate the grand total of all items
function calculateGrandTotal() {
    const totalInputs = document.querySelectorAll('#orderTable [name="totalItemAmount[]"]');
    const grandTotal = Array.from(totalInputs).reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);

    const celkemInput = document.querySelector('[name="totalServiceAmount"]');
    if (celkemInput) {
        celkemInput.value = grandTotal.toFixed(0);
    } else {
        console.error('The "Celkem" input was not found.');
    }
}

// Function to calculate the total amount due
function calculateAmountDue() {
    const grandTotal = Array.from(document.querySelectorAll('#orderTable [name="totalItemAmount[]"]'))
                            .reduce((acc, input) => acc + (parseFloat(input.value) || 0), 0);

    const oldBalance = parseFloat(document.querySelector('[name="oldBalance"]')?.value || 0);
    const amountDue = grandTotal + oldBalance;

    const amountDueInput = document.querySelector('[name="amountDue"]');
    if (amountDueInput) {
        amountDueInput.value = amountDue.toFixed(0);
    } else {
        console.error('The "amountDue" input was not found.');
    }
}

// Function to update the unit price and total based on the selected item
function updatePriceAndTotal(itemNameInput) {
    const row = itemNameInput.closest('tr');
    const unitPriceInput = row.querySelector('[name="unitPrice[]"]');
    const quantityInput = row.querySelector('[name="quantity[]"]');
    const totalInput = row.querySelector('[name="totalItemAmount[]"]');
    const itemName = itemNameInput.value.trim();

    if (window.itemPrices && window.itemPrices[itemName]) {
        let selectedPrice = window.selectedCenak === 'A' ? window.itemPrices[itemName].cenaA : window.itemPrices[itemName].cenaB;

        if (typeof selectedPrice === 'number' && !isNaN(selectedPrice)) {
            unitPriceInput.value = selectedPrice.toFixed(0);
        } else {
            console.error(`Price for ${itemName} is not a valid number`);
            unitPriceInput.value = '';
        }
    } else {
        console.log("Item not found in the product list or prices not loaded.");
        unitPriceInput.value = '';
    }

    const unitPrice = parseFloat(unitPriceInput.value) || 0;
    const quantity = parseFloat(quantityInput.value) || 0;
    const total = quantity * unitPrice;

    totalInput.value = total.toFixed(0);
    calculateGrandTotal();
    calculateAmountDue();
}

// Function to reset user inputs in the form
function resetUserInputs() {
    document.querySelectorAll('input').forEach(input => {
        input.value = '';
    });
}

// Function to fetch driver names and populate the driver dropdown
function fetchDriverNames() {
    fetch('SideBar/BackEnd/assets/calisanlar.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('driverName');
            select.innerHTML = '';

            if (data.error) {
                console.error(data.error);
                select.innerHTML = '<option value="">Error loading drivers</option>';
            } else {
                data.forEach(driver => {
                    const option = document.createElement('option');
                    option.value = driver.name;
                    option.textContent = driver.name;
                    select.appendChild(option);
                });
                applyDropdownStyles(select);
            }
        })
        .catch(error => {
            console.error('Error fetching drivers:', error);
            document.getElementById('driverName').innerHTML = '<option value="">Error loading drivers</option>';
        });
}

// Function to apply custom styles to the driver dropdown
function applyDropdownStyles(selectElement) {
    selectElement.style.backgroundColor = '#f0f0f0';
    selectElement.style.color = '#333';
    selectElement.style.padding = '10px';
    selectElement.style.borderRadius = '5px';
    selectElement.style.width = '100%';
    selectElement.style.boxSizing = 'border-box';

    selectElement.addEventListener('mouseover', () => {
        selectElement.style.backgroundColor = '#e0e0e0';
    });

    selectElement.addEventListener('mouseout', () => {
        selectElement.style.backgroundColor = '#f0f0f0';
    });

    selectElement.addEventListener('focus', () => {
        selectElement.style.outline = 'none';
        selectElement.style.borderColor = '#007bff';
    });

    selectElement.addEventListener('blur', () => {
        selectElement.style.borderColor = '#ccc';
    });
}

// Function to fetch vendors and populate the vendor datalist
function fetchVendors() {
    console.log("Fetching vendors...");
    fetch('SideBar/BackEnd/assets/fetch_vendors.php')
        .then(response => response.text())
        .then(text => {
            console.log('Server response:', text);
            try {
                return JSON.parse(text);
            } catch (error) {
                console.error('Error parsing JSON:', error);
                throw new Error('Invalid JSON response from server');
            }
        })
        .then(data => {
            const datalist = document.getElementById('orderNumberList');
            datalist.innerHTML = '';

            if (data.error) {
                console.error(data.error);
            } else {
                data.forEach(vendor => {
                    const option = document.createElement('option');
                    option.value = vendor.customer_name;
                    option.dataset.ico = vendor.ico;
                    option.dataset.dic = vendor.dic;
                    option.dataset.address = vendor.address;
                    option.dataset.companyName = vendor.company_name;
                    option.dataset.oldBalance = vendor.equity;
                    datalist.appendChild(option);
                });
                console.log("Vendors populated successfully.");
                document.getElementById('orderNumber').addEventListener('input', populateVendorDetails);
            }
        })
        .catch(error => {
            console.error('Error fetching vendors:', error);
        });
}

// Function to populate vendor details when a vendor is selected
function populateVendorDetails() {
    const input = document.getElementById('orderNumber');
    const options = document.getElementById('orderNumberList').options;
    const selectedOption = Array.from(options).find(option => option.value === input.value);

    if (selectedOption) {
        document.getElementById('icoNumber').value = selectedOption.dataset.ico;
        document.getElementById('dicNumber').value = selectedOption.dataset.dic;
        document.getElementById('deliveryAddress').value = selectedOption.dataset.address;
        document.getElementById('companyName').value = selectedOption.dataset.companyName;

        // Round the oldBalance value to the nearest integer
        const oldBalance = Math.round(parseFloat(selectedOption.dataset.oldBalance) || 0);
        document.querySelector('input[name="oldBalance"]').value = oldBalance;

    } else {
        document.getElementById('icoNumber').value = '';
        document.getElementById('dicNumber').value = '';
        document.getElementById('deliveryAddress').value = '';
        document.getElementById('companyName').value = '';
        document.querySelector('input[name="oldBalance"]').value = '';
    }
}


// Function to generate and download the PDF
function generatePDF() {
    try {
        const pdfWidthInPoints = 1300;
        const pointsToMM = 0.352778;
        const pdfWidthInMM = pdfWidthInPoints * pointsToMM;

        const doc = new jspdf.jsPDF({
            orientation: 'p',
            unit: 'mm',
            format: [pdfWidthInMM, 297]
        });

        doc.setFont(doc.getFontList().CustomFont ? "CustomFont" : "helvetica");
        doc.setFontSize(20);
        doc.text("Dodací List", 10, 20);

        function getElementValue(id) {
            const element = document.getElementById(id);
            return element ? element.value : '';
        }

        const leftSideDetails = [
            { label: 'Odberatel', value: getElementValue("orderNumber") },
            { label: 'Dodací Adresa', value: getElementValue("deliveryAddress") },
            { label: 'ICO', value: getElementValue("icoNumber") },
            { label: 'DIC', value: getElementValue("dicNumber") },
            { label: 'Název', value: getElementValue("companyName") }
        ];

        const rightSideDetails = [
            { label: 'Datum', value: formatDate(getElementValue("orderDate")) },
            { label: 'Dodací Líst Císlo', value: getElementValue("deliveryNoteNumber") },
            { label: 'Ridic', value: getElementValue("driverName") }
        ];

        function detailsToTableData(details) {
            return details.map(detail => [detail.label, detail.value]);
        }

        const tableOptions = {
            margin: { top: 5 },
            styles: { fontSize: 15, cellPadding: 3, overflow: 'linebreak' },
            bodyStyles: { fillColor: [255, 255, 255], textColor: [0, 0, 0], valign: 'middle' },
            columnStyles: { 0: { cellWidth: 60 }, 1: { cellWidth: 50 } },
            theme: 'grid'
        };

        doc.autoTable({
            body: detailsToTableData(leftSideDetails),
            startY: 40,
            startX: 10,
            ...tableOptions
        });

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

        let orderItems = [];
        const orderTable = document.getElementById("orderTable");
        if (orderTable) {
            const rows = orderTable.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let rowData = [];
                row.querySelectorAll("td input").forEach(input => {
                    rowData.push(input.value || "");
                });
                orderItems.push(rowData);
            });
        } else {
            console.warn("Order table not found");
        }

        if (orderItems.length > 0) {
            let orderItemsStartY = doc.lastAutoTable.finalY + 15;

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

        let rightSideTableDetails = [];
        const rightSideTable = document.querySelector(".right-side-table-container table");
        if (rightSideTable) {
            const rows = rightSideTable.querySelectorAll("tbody tr");
            rows.forEach(row => {
                let rowData = [];
                rowData.push(row.querySelector("th").innerText);
                const input = row.querySelector("td input");
                rowData.push(input ? input.value : 'N/A');
                rightSideTableDetails.push(rowData);
            });
        } else {
            console.warn("Right side table not found");
        }

        if (rightSideTableDetails.length > 0) {
            let rightSideTableStartX = 180;
            let rightSideTableStartY = doc.lastAutoTable.finalY + 30;
            const cellHeight = 10;
            const cellWidth = 100;
            const lineSpacing = 1;
            const labelWidth = 45;
            const textPadding = 5;

            rightSideTableDetails.forEach((detail, index) => {
                let currentY = rightSideTableStartY + (index * (cellHeight + lineSpacing));

                doc.setFillColor(200, 200, 200);
                doc.rect(rightSideTableStartX, currentY, labelWidth, cellHeight, 'F');
                doc.setFillColor(230, 230, 230);
                doc.rect(rightSideTableStartX + labelWidth, currentY, cellWidth - labelWidth, cellHeight, 'F');

                doc.setFontSize(14);
                doc.setTextColor(0, 0, 0);

                let textY = currentY + (cellHeight / 2) + (doc.internal.getLineHeight() / 3) - 3;

                doc.text(detail[0], rightSideTableStartX + textPadding, textY);
                doc.text(detail[1], rightSideTableStartX + labelWidth + textPadding, textY);
                
            });
        } else {
            console.warn("No right side table details to add to the table");
        }

        const deliveryNoteNumber = getElementValue('deliveryNoteNumber');
        const filename = "Dodací_list_číslo_" + (deliveryNoteNumber || 'unknown') + ".pdf";

        doc.save(filename);
        console.log("PDF saved as", filename);
    } catch (error) {
        console.error("An error occurred while generating the PDF:", error);
        alert("There was an error generating the PDF. Please check the console for more details.");
    }
}

// Helper function to format dates
function formatDate(dateStr) {
    if (!dateStr) return '';
    let date = new Date(dateStr);
    let day = date.getDate().toString().padStart(2, '0');
    let month = (date.getMonth() + 1).toString().padStart(2, '0');
    let year = date.getFullYear();
    return `${day}.${month}.${year}`;
}

// Event listener to check if PDF should be generated on page load
window.addEventListener('DOMContentLoaded', (event) => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('generatePDF') && urlParams.get('generatePDF') === 'true') {
        generatePDF();
    }
});






















