@php
    $pageTitle = "Receipt Management";
    $currentPage = "receipt";
@endphp

@include('layout.header')
@include('layout.sidebar')

<style>
    .receipt-card{
        background: #fff;
        border-radius: 8px;
        padding: 16px;
        margin: 16px;
    }

    .receipt-topbar{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .receipt-title{
        font-size: 16px;
        font-weight: 700;
        color: #4b5b73;
    }

    .receipt-filters{
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .receipt-filters select,
    .receipt-filters input{
        height: 34px;
        border: 1px solid #d8dee8;
        border-radius: 6px;
        padding: 0 12px;
        background: #fff;
        color: #5b677a;
        outline: none;
    }

    .receipt-filters select{
        min-width: 96px;
    }

    .search-box-receipt{
        position: relative;
    }

    .search-box-receipt input{
        min-width: 180px;
        padding-right: 34px;
    }

    .search-box-receipt i{
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a6;
    }

    .hidden-btn,
    .refresh-btn{
        height: 34px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
    }

    .hidden-btn{
        border: 1px solid #ffd8dc;
        background: #fff;
        color: #ef5b6b;
        padding: 0 12px;
    }

    .refresh-btn{
        width: 38px;
        border: 1px solid #d8dee8;
        background: #fff;
        color: #5b677a;
    }

    .table-wrap{
        overflow-x: auto;
    }

    .receipt-table{
        width: 100%;
        border-collapse: collapse;
        min-width: 1350px;
    }

    .receipt-table th,
    .receipt-table td{
        border-bottom: 1px solid #edf1f5;
        padding: 12px 10px;
        text-align: left;
        font-size: 14px;
        color: #66748a;
        vertical-align: middle;
        background: #fff;
    }

    .receipt-table th{
        font-weight: 700;
        color: #5a6880;
        background: #fafbfd;
    }

    .receipt-link{
        color: #5d6fff;
        text-decoration: none;
        font-weight: 500;
    }

    .amount{
        font-weight: 600;
        color: #4f5f78;
    }

    .active-badge{
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: #daf5dc;
        color: #28a745;
        white-space: nowrap;
    }

    .dots-btn{
        border: none;
        background: transparent;
        color: #b0b8c5;
        font-size: 18px;
        cursor: pointer;
    }

    .table-footer{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 14px;
        gap: 10px;
        flex-wrap: wrap;
    }

    .footer-left{
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4d5d75;
        font-size: 14px;
    }

    .footer-left input{
        width: 34px;
        height: 30px;
        border: 1px solid #d8dee8;
        border-radius: 4px;
        text-align: center;
        outline: none;
    }

    .go-btn{
        height: 30px;
        border: none;
        background: #4aa3df;
        color: #fff;
        border-radius: 4px;
        padding: 0 12px;
        cursor: pointer;
    }

    .footer-right{
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .page-btn{
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 6px;
        background: transparent;
        color: #6c7b91;
        cursor: pointer;
    }

    .page-btn.active{
        background: #4aa3df;
        color: #fff;
    }
</style>

<main class="main">
    <div class="receipt-card">
        <div class="receipt-topbar">
            <div class="receipt-title">Total : 5070</div>

            <div class="receipt-filters">
                <select>
                    <option>All</option>
                    <option>General</option>
                    <option>KMall</option>
                </select>

                <div class="search-box-receipt">
                    <input type="text" placeholder="Search..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <button class="hidden-btn">
                    <i class="fa-regular fa-triangle-exclamation"></i> Hidden
                </button>

                <button class="refresh-btn">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Receipt Number</th>
                        <th>Order Number</th>
                        <th>Customer Name</th>
                        <th>Customer Type</th>
                        <th>Phone</th>
                        <th>Total Amount</th>
                        <th>Receipt Date</th>
                        <th>Include Tax</th>
                        <th>Print Status</th>
                        <th>Status</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>1</td>
                        <td><a href="#" class="receipt-link">LD26-004625</a></td>
                        <td><a href="#" class="receipt-link">LDO001489</a></td>
                        <td>Thita Puth</td>
                        <td>General</td>
                        <td>086481707</td>
                        <td class="amount">42.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td><a href="#" class="receipt-link">FSC26-000017</a></td>
                        <td><a href="#" class="receipt-link">FSC000017</a></td>
                        <td>Lily White</td>
                        <td>General</td>
                        <td>010774477</td>
                        <td class="amount">133.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td><a href="#" class="receipt-link">LD26-004624</a></td>
                        <td><a href="#" class="receipt-link">LDO001488</a></td>
                        <td>Nou Channy</td>
                        <td>General</td>
                        <td>089874212016688773</td>
                        <td class="amount">26.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Printed</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td><a href="#" class="receipt-link">LD26-004623</a></td>
                        <td><a href="#" class="receipt-link">LDO001487</a></td>
                        <td>Nheb Chenda</td>
                        <td>General</td>
                        <td>85595227778</td>
                        <td class="amount">36.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td><a href="#" class="receipt-link">LD26-004622</a></td>
                        <td><a href="#" class="receipt-link">LDO001486</a></td>
                        <td>Ly Ta</td>
                        <td>General</td>
                        <td>092619678</td>
                        <td class="amount">154.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td><a href="#" class="receipt-link">LD26-004621</a></td>
                        <td><a href="#" class="receipt-link">LDO001485</a></td>
                        <td>YA DA</td>
                        <td>General</td>
                        <td>093356665</td>
                        <td class="amount">126.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>7</td>
                        <td><a href="#" class="receipt-link">LD26-004620</a></td>
                        <td><a href="#" class="receipt-link">LDO001484</a></td>
                        <td>Kon Ko</td>
                        <td>General</td>
                        <td>017277251</td>
                        <td class="amount">37.50</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td><a href="#" class="receipt-link">LMB26-000046</a></td>
                        <td><a href="#" class="receipt-link">LMB000047</a></td>
                        <td>Kmall</td>
                        <td>KMall</td>
                        <td>2</td>
                        <td class="amount">2.30</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>9</td>
                        <td><a href="#" class="receipt-link">LMB26-000045</a></td>
                        <td><a href="#" class="receipt-link">LMB000046</a></td>
                        <td>Kmall</td>
                        <td>KMall</td>
                        <td>2</td>
                        <td class="amount">14.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>10</td>
                        <td><a href="#" class="receipt-link">LMB26-000044</a></td>
                        <td><a href="#" class="receipt-link">LMB000045</a></td>
                        <td>LD Peng Houth</td>
                        <td>LD Peng Houth</td>
                        <td>3</td>
                        <td class="amount">29.00</td>
                        <td>12/04/2026</td>
                        <td>no</td>
                        <td>Not printed yet</td>
                        <td><span class="active-badge">Active</span></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="footer-left">
                <span>Total of : 102</span>
                <span>Go to</span>
                <input type="text" value="1">
                <button class="go-btn">Go</button>
            </div>

            <div class="footer-right">
                <button class="page-btn"><i class="fa-solid fa-angle-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">4</button>
                <button class="page-btn">102</button>
                <button class="page-btn"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </div>
    </div>
</main>

@include('layout.footer')