<?php
$pageTitle = $pageTitle ?? '';
$currentPage = $currentPage ?? '';
?>

<div class="app">
    <aside class="sidebar">
        <div class="sidebar-section">

            <a href="index.php" class="menu-item <?= ($currentPage == 'dashboard') ? 'active' : '' ?>">
                <i class="fa-solid fa-table-cells-large"></i>
                <span>Dashboard</span>
            </a>

            <a href="pos.php" class="menu-item <?= ($currentPage == 'pos') ? 'active' : '' ?>">
                <i class="fa-solid fa-shop"></i>
                <span>POS</span>
            </a>

            <a href="order.php" class="menu-item <?= ($currentPage == 'order') ? 'active' : '' ?>">
                <i class="fa-regular fa-clipboard"></i>
                <span>Order</span>
            </a>

            <a href="invoice.php" class="menu-item <?= ($currentPage == 'invoice') ? 'active' : '' ?>">
                <i class="fa-regular fa-file-lines"></i>
                <span>Invoices</span>
            </a>

            <a href="receipt.php" class="menu-item <?= ($currentPage == 'receipt') ? 'active' : '' ?>">
                <i class="fa-regular fa-rectangle-list"></i>
                <span>Receipt</span>
            </a>

            <div class="menu-group <?= in_array($currentPage, ['customers_categories','customers_products','customers_variants','customers_set_menu','customers_combo_set','customers_options','customers_discounts','inventory_categories','inventory_products','inventory_variants','inventory_set_menu','inventory_combo_set']) ? 'open' : '' ?>">
                <div class="menu-item submenu-toggle <?= in_array($currentPage, ['customers_categories','customers_products','customers_variants','customers_set_menu','customers_combo_set','customers_options','customers_discounts','inventory_categories','inventory_products','inventory_variants','inventory_set_menu','inventory_combo_set']) ? 'active' : '' ?>">
                    <i class="fa-solid fa-boxes-stacked"></i>
                    <span>Item Management</span>
                    <i class="fa-solid fa-angle-down right"></i>
                </div>

                <div class="submenu">

                    <div class="submenu-title submenu-toggle-mini <?= in_array($currentPage, ['customers_categories','customers_products','customers_variants','customers_set_menu','customers_combo_set','customers_options','customers_discounts']) ? 'active' : '' ?>">
                        <span class="dot"></span>
                        <span>Customers</span>
                        <i class="fa-solid fa-angle-down right"></i>
                    </div>

                    <div class="submenu-inner <?= in_array($currentPage, ['customers_categories','customers_products','customers_variants','customers_set_menu','customers_combo_set','customers_options','customers_discounts']) ? 'open' : '' ?>">
                        <a href="customers-categories.php" class="submenu-item <?= ($currentPage == 'customers_categories') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Categories</span>
                        </a>

                        <a href="customers-products.php" class="submenu-item <?= ($currentPage == 'customers_products') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>All Products</span>
                        </a>

                        <a href="customers-variants.php" class="submenu-item <?= ($currentPage == 'customers_variants') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Variants</span>
                        </a>

                        <a href="customers-set-menu.php" class="submenu-item <?= ($currentPage == 'customers_set_menu') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Set Menu</span>
                        </a>

                        <a href="customers-combo-set.php" class="submenu-item <?= ($currentPage == 'customers_combo_set') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Combo Set</span>
                        </a>

                        <a href="customers-options.php" class="submenu-item <?= ($currentPage == 'customers_options') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Options</span>
                        </a>

                        <a href="customers-discounts.php" class="submenu-item <?= ($currentPage == 'customers_discounts') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Discounts</span>
                        </a>
                    </div>

                    <div class="submenu-title submenu-toggle-mini <?= in_array($currentPage, ['inventory_categories','inventory_products','inventory_variants','inventory_set_menu','inventory_combo_set']) ? 'active' : '' ?>">
                        <span class="dot"></span>
                        <span>Inventory</span>
                        <i class="fa-solid fa-angle-down right"></i>
                    </div>

                    <div class="submenu-inner <?= in_array($currentPage, ['inventory_categories','inventory_products','inventory_variants','inventory_set_menu','inventory_combo_set']) ? 'open' : '' ?>">
                        <a href="inventory-categories.php" class="submenu-item <?= ($currentPage == 'inventory_categories') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Categories</span>
                        </a>

                        <a href="inventory-products.php" class="submenu-item <?= ($currentPage == 'inventory_products') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>All Products</span>
                        </a>

                        <a href="inventory-variants.php" class="submenu-item <?= ($currentPage == 'inventory_variants') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Variants</span>
                        </a>

                        <a href="inventory-set-menu.php" class="submenu-item <?= ($currentPage == 'inventory_set_menu') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Set Menu</span>
                        </a>

                        <a href="inventory-combo-set.php" class="submenu-item <?= ($currentPage == 'inventory_combo_set') ? 'active' : '' ?>">
                            <span class="dot"></span>
                            <span>Combo Set</span>
                        </a>
                    </div>

                </div>
            </div>

            <a href="stock.php" class="menu-item <?= ($currentPage == 'stock') ? 'active' : '' ?>">
                <i class="fa-solid fa-warehouse"></i>
                <span>Stock Inventory</span>
                <i class="fa-solid fa-angle-down right"></i>
            </a>

            <a href="users.php" class="menu-item <?= ($currentPage == 'users') ? 'active' : '' ?>">
                <i class="fa-regular fa-user"></i>
                <span>User Management</span>
                <i class="fa-solid fa-angle-down right"></i>
            </a>

            <a href="report.php" class="menu-item <?= ($currentPage == 'report') ? 'active' : '' ?>">
                <i class="fa-regular fa-chart-bar"></i>
                <span>Report</span>
                <i class="fa-solid fa-angle-down right"></i>
            </a>

            <a href="homepage.php" class="menu-item <?= ($currentPage == 'homepage') ? 'active' : '' ?>">
                <i class="fa-solid fa-panorama"></i>
                <span>Home Page</span>
            </a>

            <a href="feedback.php" class="menu-item <?= ($currentPage == 'feedback') ? 'active' : '' ?>">
                <i class="fa-regular fa-message"></i>
                <span>Feedback</span>
            </a>

            <a href="setting.php" class="menu-item <?= ($currentPage == 'setting') ? 'active' : '' ?>">
                <i class="fa-solid fa-gear"></i>
                <span>Setting</span>
                <i class="fa-solid fa-angle-down right"></i>
            </a>

            <a href="pages.php" class="menu-item <?= ($currentPage == 'pages') ? 'active' : '' ?>">
                <i class="fa-regular fa-folder"></i>
                <span>Page Management</span>
                <i class="fa-solid fa-angle-down right"></i>
            </a>

            <a href="logout.php" class="menu-item">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
                <span>Sign Out</span>
            </a>

        </div>
    </aside>

    <section class="content">
        <header class="topbar">
            <div class="topbar-left">
                <div class="hamburger">
                    <i class="fa-solid fa-bars"></i>
                </div>
                <div class="page-title"><?php echo $pageTitle; ?></div>
            </div>

            <div class="topbar-right">
                <div class="lang-badge">🇰🇭</div>
                <div class="lang-badge">🇬🇧</div>
                <div class="user-chip">
                    <span>SUPER ADMIN</span>
                    <div class="avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <i class="fa-solid fa-angle-down"></i>
                </div>
            </div>
        </header><script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.submenu-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const group = this.closest('.menu-group');
            group.classList.toggle('open');
        });
    });

    document.querySelectorAll('.submenu-toggle-mini').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const next = this.nextElementSibling;
            if (next) {
                next.classList.toggle('open');
            }
        });
    });
});
</script>