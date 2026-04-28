@php
    $pageTitle = "Category Management";
    $currentPage = "customers_categories";
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="category-card">
        <div class="category-topbar">
            <div class="category-title">Total : 40</div>

            <div class="category-filters">
                <select>
                    <option>Shop</option>
                    <option>Little Duckling - SMC</option>
                    <option>Little Meatbox</option>
                    <option>Pizza Time</option>
                </select>

                <select>
                    <option>Branch</option>
                    <option>Five Star Chef - SMC</option>
                    <option>Little Duckling - SMC</option>
                    <option>Little Meatbox</option>
                </select>

                <div class="search-box-category">
                    <input type="text" placeholder="Search..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <button class="create-btn">
                    <i class="fa-solid fa-plus"></i> CREATE NEW
                </button>

                <button class="trash-btn">
                    <i class="fa-solid fa-trash"></i> Trash
                </button>

                <button class="refresh-btn">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table class="category-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Branch</th>
                        <th>Code</th>
                        <th>Image</th>
                        <th>Title (English)</th>
                        <th>Title (Khmer)</th>
                        <th>Total Items</th>
                        <th>Status</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Five Star Chef - SMC</td>
                        <td>FSC-G</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/1046/1046784.png" alt="GOOSE SET"></td>
                        <td>GOOSE SET</td>
                        <td>ប៊ូយក្ងាន</td>
                        <td>1</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td>Five Star Chef - SMC</td>
                        <td>FSC-CS</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/3082/3082031.png" alt="COMBO SET"></td>
                        <td>COMBO SET</td>
                        <td>COMBO SET</td>
                        <td>3</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td>Five Star Chef - SMC</td>
                        <td>FSC-S</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/5787/5787016.png" alt="SIDE DISHES"></td>
                        <td>SIDE DISHES (SET)</td>
                        <td>SIDE DISHES (SET)</td>
                        <td>17</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td>Five Star Chef - SMC</td>
                        <td>FSC-E</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/3652/3652191.png" alt="EVENT"></td>
                        <td>EVENT</td>
                        <td>EVENT</td>
                        <td>3</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td>Little Duckling - SMC</td>
                        <td>LD-S-SV</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/3174/3174880.png" alt="SERVICE"></td>
                        <td>SERVICE</td>
                        <td>សេវាកម្ម</td>
                        <td>1</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td>Five Star Chef - SMC</td>
                        <td>FSC-SD</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/2515/2515183.png" alt="SIDE DISHES"></td>
                        <td>SIDE DISHES</td>
                        <td>SIDE DISHES</td>
                        <td>14</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>7</td>
                        <td>Little Meatbox</td>
                        <td>LMB-VG</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/2153/2153788.png" alt="VEGETABLES"></td>
                        <td>VEGETABLES</td>
                        <td>បន្លែ</td>
                        <td>7</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td>Little Meatbox</td>
                        <td>LMB-SC</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/3075/3075977.png" alt="SAUCE"></td>
                        <td>SAUCE</td>
                        <td>ទឹកជ្រលក់</td>
                        <td>7</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>9</td>
                        <td>Little Meatbox</td>
                        <td>LMB-MT</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/1046/1046769.png" alt="MEAT"></td>
                        <td>MEAT</td>
                        <td>សាច់</td>
                        <td>8</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>10</td>
                        <td>Pizza Time</td>
                        <td>PT-ST</td>
                        <td><img class="category-image" src="https://cdn-icons-png.flaticon.com/512/3480/3480823.png" alt="SPAGHETTI"></td>
                        <td>SPAGHETTI</td>
                        <td>ស្ប៉ាហ្គេតទី</td>
                        <td>3</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="footer-left">
                <span>Total of : 1</span>
                <span>Go to</span>
                <input type="text" value="1">
                <button class="go-btn">Go</button>
            </div>

            <div class="footer-right">
                <button class="page-btn"><i class="fa-solid fa-angle-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </div>
    </div>
</main>

@include('layout.footer')