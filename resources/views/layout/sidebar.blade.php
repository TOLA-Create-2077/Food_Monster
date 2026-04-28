<aside class="sidebar">

    {{-- LOGO --}}
    <div style="display:flex; justify-content:center; align-items:center; padding:15px 10px 26px;">
        <a href="{{ url('/') }}">
            <img src="{{ asset('logo.jpg') }}" alt="Logo" style="width:95px; display:block;">
        </a>
    </div>

    <div class="sidebar-section">

        <a href="{{ url('/') }}" class="menu-item {{ $currentPage == 'dashboard' ? 'active' : '' }}">
            <i class="fa-solid fa-table-cells-large" style="width:20px; text-align:center;"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ url('/pos') }}" class="menu-item {{ $currentPage == 'pos' ? 'active' : '' }}">
            <i class="fa-solid fa-shop" style="width:20px; text-align:center;"></i>
            <span>POS</span>
        </a>

        <a href="{{ url('/order') }}" class="menu-item {{ $currentPage == 'order' ? 'active' : '' }}">
            <i class="fa-regular fa-clipboard" style="width:20px; text-align:center;"></i>
            <span>Order</span>
        </a>

        <a href="{{ url('/invoice') }}" class="menu-item {{ $currentPage == 'invoice' ? 'active' : '' }}">
            <i class="fa-regular fa-file-lines" style="width:20px; text-align:center;"></i>
            <span>Invoices</span>
        </a>

        <a href="{{ url('/receipt') }}" class="menu-item {{ $currentPage == 'receipt' ? 'active' : '' }}">
            <i class="fa-regular fa-rectangle-list" style="width:20px; text-align:center;"></i>
            <span>Receipt</span>
        </a>

        @php
            $customerPages = [
                'customers_categories','customers_products','customers_variants',
                'customers_set_menu','customers_combo_set','customers_options','customers_discounts'
            ];

            $inventoryPages = [
                'inventory_categories','inventory_products','inventory_variants',
                'inventory_set_menu','inventory_combo_set'
            ];

            $itemPages = array_merge($customerPages, $inventoryPages);
        @endphp

        <div class="menu-group {{ in_array($currentPage, $itemPages) ? 'open' : '' }}">

            <div class="menu-item submenu-toggle {{ in_array($currentPage, $itemPages) ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked" style="width:20px; text-align:center;"></i>
                <span>Item Management</span>
                <i class="fa-solid fa-angle-down right"></i>
            </div>

            <div class="submenu">

                <div class="submenu-title submenu-toggle-mini {{ in_array($currentPage, $customerPages) ? 'active' : '' }}">
                    <span class="dot"></span>
                    <span>Customers</span>
                    <i class="fa-solid fa-angle-down right"></i>
                </div>

                <div class="submenu-inner {{ in_array($currentPage, $customerPages) ? 'open' : '' }}">
                    <a href="{{ url('/customers/categories') }}" class="submenu-item {{ $currentPage == 'customers_categories' ? 'active' : '' }}">
                        <span class="dot"></span><span>Categories</span>
                    </a>
                    <a href="{{ url('/customers/products') }}" class="submenu-item {{ $currentPage == 'customers_products' ? 'active' : '' }}">
                        <span class="dot"></span><span>All Products</span>
                    </a>
                    <a href="{{ url('/customers/variants') }}" class="submenu-item {{ $currentPage == 'customers_variants' ? 'active' : '' }}">
                        <span class="dot"></span><span>Variants</span>
                    </a>
                    <a href="{{ url('/customers/set-menu') }}" class="submenu-item {{ $currentPage == 'customers_set_menu' ? 'active' : '' }}">
                        <span class="dot"></span><span>Set Menu</span>
                    </a>
                    <a href="{{ url('/customers/combo-set') }}" class="submenu-item {{ $currentPage == 'customers_combo_set' ? 'active' : '' }}">
                        <span class="dot"></span><span>Combo Set</span>
                    </a>
                    <a href="{{ url('/customers/options') }}" class="submenu-item {{ $currentPage == 'customers_options' ? 'active' : '' }}">
                        <span class="dot"></span><span>Options</span>
                    </a>
                    <a href="{{ url('/customers/discounts') }}" class="submenu-item {{ $currentPage == 'customers_discounts' ? 'active' : '' }}">
                        <span class="dot"></span><span>Discounts</span>
                    </a>
                </div>

                <div class="submenu-title submenu-toggle-mini {{ in_array($currentPage, $inventoryPages) ? 'active' : '' }}">
                    <span class="dot"></span>
                    <span>Inventory</span>
                    <i class="fa-solid fa-angle-down right"></i>
                </div>

                <div class="submenu-inner {{ in_array($currentPage, $inventoryPages) ? 'open' : '' }}">
                    <a href="{{ url('/inventory/categories') }}" class="submenu-item {{ $currentPage == 'inventory_categories' ? 'active' : '' }}">
                        <span class="dot"></span><span>Categories</span>
                    </a>
                    <a href="{{ url('/inventory/products') }}" class="submenu-item {{ $currentPage == 'inventory_products' ? 'active' : '' }}">
                        <span class="dot"></span><span>All Products</span>
                    </a>
                    <a href="{{ url('/inventory/variants') }}" class="submenu-item {{ $currentPage == 'inventory_variants' ? 'active' : '' }}">
                        <span class="dot"></span><span>Variants</span>
                    </a>
                    <a href="{{ url('/inventory/set-menu') }}" class="submenu-item {{ $currentPage == 'inventory_set_menu' ? 'active' : '' }}">
                        <span class="dot"></span><span>Set Menu</span>
                    </a>
                    <a href="{{ url('/inventory/combo-set') }}" class="submenu-item {{ $currentPage == 'inventory_combo_set' ? 'active' : '' }}">
                        <span class="dot"></span><span>Combo Set</span>
                    </a>
                </div>

            </div>
        </div>

        <a href="{{ url('/stock') }}" class="menu-item {{ $currentPage == 'stock' ? 'active' : '' }}">
            <i class="fa-solid fa-warehouse" style="width:20px; text-align:center;"></i>
            <span>Stock Inventory</span>
            <i class="fa-solid fa-angle-down right"></i>
        </a>

        <a href="{{ url('/users') }}" class="menu-item {{ $currentPage == 'users' ? 'active' : '' }}">
            <i class="fa-regular fa-user" style="width:20px; text-align:center;"></i>
            <span>User Management</span>
            <i class="fa-solid fa-angle-down right"></i>
        </a>

        <a href="{{ url('/report') }}" class="menu-item {{ $currentPage == 'report' ? 'active' : '' }}">
            <i class="fa-regular fa-chart-bar" style="width:20px; text-align:center;"></i>
            <span>Report</span>
            <i class="fa-solid fa-angle-down right"></i>
        </a>

        <a href="{{ url('/homepage') }}" class="menu-item {{ $currentPage == 'homepage' ? 'active' : '' }}">
            <i class="fa-solid fa-panorama" style="width:20px; text-align:center;"></i>
            <span>Home Page</span>
        </a>

        <a href="{{ url('/feedback') }}" class="menu-item {{ $currentPage == 'feedback' ? 'active' : '' }}">
            <i class="fa-regular fa-message" style="width:20px; text-align:center;"></i>
            <span>Feedback</span>
        </a>

        <a href="{{ url('/setting') }}" class="menu-item {{ $currentPage == 'setting' ? 'active' : '' }}">
            <i class="fa-solid fa-gear" style="width:20px; text-align:center;"></i>
            <span>Setting</span>
            <i class="fa-solid fa-angle-down right"></i>
        </a>

        <a href="{{ url('/pages') }}" class="menu-item {{ $currentPage == 'pages' ? 'active' : '' }}">
            <i class="fa-regular fa-folder" style="width:20px; text-align:center;"></i>
            <span>Page Management</span>
            <i class="fa-solid fa-angle-down right"></i>
        </a>

        <a href="{{ url('/logout') }}" class="menu-item">
            <i class="fa-solid fa-arrow-right-from-bracket" style="width:20px; text-align:center;"></i>
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

            <div class="page-title" style="font-size:22px; font-weight:700;">
                {{ $pageTitle }}
            </div>
        </div>

        <div class="topbar-right">
            <div class="lang-badge">KH</div>
            <div class="lang-badge">GB</div>

            <div class="user-chip" style="display:flex; align-items:center; gap:8px;">
                <span>SUPER ADMIN</span>

                <div class="avatar">
                    <i class="fa-solid fa-user"></i>
                </div>

                <i class="fa-solid fa-angle-down"></i>
            </div>
        </div>
    </header>