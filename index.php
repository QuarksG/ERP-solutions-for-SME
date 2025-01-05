<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}


// Start output buffering
ob_start();

// Define the menu items
$menuItems = [
    ['name' => 'Gösterge Paneli', 'href' => '?page=dashboard', 'icon' => 'fa-tachometer-alt'],
    ['name' => 'Envanter', 'href' => '?page=inventory-view', 'icon' => 'fa-boxes'],
    ['name' => 'İrsaliye Oluştur', 'href' => '?page=irsaliye', 'icon' => 'fa-file-invoice'],
    ['name' => 'Cari Irsaliyeler', 'href' => '?page=orders-view', 'icon' => 'fa-shopping-cart'],
    ['name' => 'Emanet Kasası', 'href' => '?page=emanetkasasi', 'icon' => 'fa-archive'],
    ['name' => 'Muhasebe', 'href' => '?page=muhasebe', 'icon' => 'fa-warehouse'],
    ['name' => 'Çalışanlar', 'href' => '?page=calisanlar', 'icon' => 'fa-users'],
    ['name' => 'Kafa Kağıdı', 'href' => '?page=kafa-kagidi', 'icon' => 'fa-clipboard-list'],
    ['name' => 'Müşteri Kartı', 'href' => '?page=musteri-karti', 'icon' => 'fa-id-card'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #f0f0f0;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar .brand {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .sidebar .brand i {
            font-size: 24px;
            color: #007bff;
        }
        .sidebar .brand span {
            font-size: 20px;
            font-weight: bold;
            color: #007bff;
            margin-left: 10px;
        }
        .menu-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            color: #000;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s, color 0.2s;
        }
        .menu-item i {
            font-size: 30px;
            margin-right: 10px;
        }
        .menu-item:hover {
            background-color: #007bff;
            color: #fff;
        }
        .menu-item:hover i {
            color: #fff;
        }
        .bottom-menu {
            margin-top: auto;
        }
        .content {
            margin-left: 260px; /* Adjust to ensure content is not hidden behind the sidebar */
            padding: 20px;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <div class="brand">
            <i class="fas fa-truck-moving"></i>
            <span>InventoryApp</span>
        </div>
        <div class="menu">
            <?php foreach ($menuItems as $item): ?>
                <a href="<?= $item['href'] ?>" class="menu-item">
                    <i class="fas <?= $item['icon'] ?>"></i>
                    <span><?= $item['name'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="bottom-menu">
        <a href="?page=yardim" class="menu-item">
            <i class="fas fa-question-circle"></i>
            <span>Yardım</span>
        </a>
        <a href="?page=ayarlar" class="menu-item">
            <i class="fas fa-cog"></i>
            <span>Ayarlar</span>
        </a>
    </div>
</div>

<div class="content">
    <?php
    // Get the page parameter from the URL
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

    // Define the available pages and their corresponding file paths
    $pages = [
        'dashboard' => 'SideBar/dashboard.php',
        'calisanlar' => 'SideBar/calisanlar.php',
        'emanetkasasi' => 'SideBar/emanetkasasi.php',
        'inventory-view' => 'SideBar/inventory-view.php',
        'irsaliye' => 'SideBar/irsaliye.php',
        'kafa-kagidi' => 'SideBar/kafa-kagidi.php',
        'muhasebe' => 'SideBar/muhasebe.php',
        'musteri-karti' => 'SideBar/musteri-karti.php',
        'orders-view' => 'SideBar/orders-view.php',
        'ayarlar' => 'SideBar/ayarlar.php',
        'yardim' => 'SideBar/yardim.php'
    ];

    // Include the appropriate page file if it exists, otherwise show a 404 error
    if (array_key_exists($page, $pages)) {
        include $pages[$page];
    } else {
        echo "<h1>404 Page Not Found</h1>";
    }
    ?>
</div>

</body>
</html>

<?php
// Flush the output buffer
ob_end_flush();
