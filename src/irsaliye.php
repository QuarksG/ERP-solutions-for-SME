<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İrsaliye Oluştur</title>
    <link rel="stylesheet" href="SideBar/CSS/irsaliye.css">
</head>
<body>
    <div id="content">
        <?php if (isset($_SESSION['success'])): ?>
            <script>
                alert("Data recorded successfully!");
            </script>
            <?php unset($_SESSION['success']); // Clear the success message ?>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="order-details-section">
                <h1 class="main-title">Detaily Objednávky</h1>
                <div style="display: flex; justify-content: space-between;">
                    <table class="order-details">
                        <tbody>
                            <tr>
                                <th>Odběratel:</th>
                                <td>
                                    <input list="orderNumberList" name="orderNumber" id="orderNumber" placeholder="Start typing to search vendor">
                                    <datalist id="orderNumberList"></datalist>
                                </td>
                            </tr>
                            <tr>
                                <th>Ičo:</th>
                                <td><input type="text" name="icoNumber" id="icoNumber" placeholder="Enter Ičo" readonly></td>
                            </tr>
                            <tr>
                                <th>DIČ:</th>
                                <td><input type="text" name="dicNumber" id="dicNumber" placeholder="Enter DIČ" readonly></td>
                            </tr>
                            <tr>
                                <th>Dodací Adresa:</th>
                                <td><input type="text" name="deliveryAddress" id="deliveryAddress" placeholder="Enter Dodací Adresa" readonly></td>
                            </tr>
                            <tr>
                                <th>Název:</th>
                                <td><input type="text" name="companyName" id="companyName" placeholder="Enter Name" readonly></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="order-details">
                        <tbody>
                            <tr>
                                <th>Předpok Ládaná Doba Dodání:</th>
                                <td>
                                    <input type="date" name="shippingStart"> - <input type="date" name="shippingEnd">
                                </td>
                            </tr>
                            <tr>
                                <th>Datum:</th>
                                <td><input type="date" name="orderDate" id="orderDate"></td>
                            </tr>
                            <tr>
                                <th>Dodací list číslo:</th>
                                <td><input type="text" id="deliveryNoteNumber" name="deliveryNoteNumber" readonly></td>
                            </tr>
                            <tr>
                                <th>Řidič:</th>
                                <td>
                                    <select name="driverName" id="driverName"></select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <button id="priceListA" type="button">Cena A</button>
            <button id="priceListB" type="button">Cena B</button> 
            <div class="order-items">
                <h2>Položky objednávky</h2>
                <table id="orderTable">
                    <thead>
                        <tr>
                            <th>Označení položky</th>
                            <th>Množství</th>
                            <th>Jedn.Cena</th>
                            <th>Celkem</th>
                            <th>Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="itemName[]" placeholder="Označení položky" list="itemList" oninput="showSuggestions(this)"></td>
                            <td><input type="number" name="quantity[]" placeholder="Množství" oninput="calculateTotal(this)"></td>
                            <td><input type="number" name="unitPrice[]" placeholder="Jedn.Cena" oninput="calculateTotal(this)"></td>
                            <td><input type="text" name="totalItemAmount[]" placeholder="Celkem" readonly></td>
                            <td><button type="button" onclick="addRow()">+</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <section class="financial-details" style="display: flex; justify-content: space-between; width: 98%;">
                <table id="financialTable" style="width: 98%;">
                    <tbody>
                        <tr>
                            <th style="text-align: left;">Zaplaceno v Hotovostí:</th>
                            <td><input type="number" name="cashPaid" style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Stravenky Spocitane:</th>
                            <td><input type="number" name="mealVouchersCounted" style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Procento:</th>
                            <td><input type="number" name="percentage" style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Stravenky Ciste:</th>
                            <td><input type="number" name="mealVouchersClean" style="width: 60%;"></td>
                        </tr>
                        <tr>
                            <th style="text-align: left;">Novy dluh Cely:</th>
                            <td><input type="number" name="newTotalDebt" style="width: 60%;"></td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <div class="right-side-table-container">
                <div class="right-side-table">
                    <table>
                        <tbody>
                            <tr>
                                <th style="text-align: left;">Celkem:</th>
                                <td><input type="text" name="totalServiceAmount" placeholder="Celkem" readonly></td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Stary Zustatek:</th>
                                <td><input type="text" name="oldBalance" placeholder="Starý Zustatek"></td>
                            </tr>
                            <tr>
                                <th style="text-align: left;">Castka k Uhrade:</th>
                                <td><input type="text" name="amountDue" placeholder="Cástka k Uhradě"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="button-container">
                <button id="recordButton" class="rounded-button" type="submit">Záznam jako</button>
                <button class="rounded-button" type="button" onclick="generatePDF()">Stáhnout jako PDF</button>
            </div>
        </form>
        <footer>
            <span>Reserved by Author</span>
            <span>@MIT</span>
        </footer>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.21/jspdf.plugin.autotable.min.js"></script>
    <script src="SideBar/JS/irsaliye.js"></script>
</body>
</html>
