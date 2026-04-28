@php
    $pageTitle = "Invoices";
    $currentPage = "invoice";
@endphp

@include('layout.header')
@include('layout.sidebar')

<style>
    .invoice-card{
        background: #fff;
        border-radius: 8px;
        padding: 16px;
        margin: 16px;
    }

    .invoice-topbar{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .invoice-title{
        font-size: 16px;
        font-weight: 700;
        color: #4b5b73;
    }

    .invoice-filters{
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }

    .invoice-filters select,
    .invoice-filters input{
        height: 34px;
        border: 1px solid #d8dee8;
        border-radius: 6px;
        padding: 0 12px;
        background: #fff;
        color: #5b677a;
        min-width: 145px;
        outline: none;
    }

    .search-box-invoice{
        position: relative;
    }

    .search-box-invoice input{
        padding-right: 34px;
        min-width: 180px;
    }

    .search-box-invoice i{
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #8a94a6;
    }

    .filter-btn,
    .refresh-btn{
        height: 34px;
        border: none;
        border-radius: 6px;
        padding: 0 14px;
        cursor: pointer;
        font-weight: 600;
    }

    .filter-btn{
        background: #2f89d9;
        color: #fff;
    }

    .refresh-btn{
        width: 38px;
        padding: 0;
        background: #fff;
        border: 1px solid #d8dee8;
        color: #5b677a;
    }

    .table-wrap{
        overflow-x: auto;
    }

    .invoice-table{
        width: 100%;
        border-collapse: collapse;
        min-width: 1450px;
    }

    .invoice-table th,
    .invoice-table td{
        border-bottom: 1px solid #edf1f5;
        padding: 12px 10px;
        text-align: left;
        font-size: 14px;
        color: #66748a;
        vertical-align: middle;
        background: #fff;
    }

    .invoice-table th{
        font-weight: 700;
        color: #5a6880;
        background: #fafbfd;
    }

    .invoice-link{
        color: #527dff;
        text-decoration: none;
        font-weight: 500;
    }

    .amount{
        font-weight: 600;
        color: #4f5f78;
    }

    .paid-badge{
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: #e8f7ef;
        color: #22a06b;
        white-space: nowrap;
    }

    .pending-badge{
        display: inline-block;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        background: #fff3d9;
        color: #d18a00;
        white-space: nowrap;
    }

    .dots-btn,
    .print-btn{
        border: none;
        background: transparent;
        color: #97a3b5;
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
    <div class="invoice-card">
        <div class="invoice-topbar">
            <div class="invoice-title">All Invoices : 12</div>

            <div class="invoice-filters">
                <select>
                    <option>All</option>
                    <option>PAID</option>
                    <option>PENDING</option>
                </select>

                <select>
                    <option>All Branch</option>
                    <option>Little Duckling - SMC</option>
                    <option>Little Meatbox</option>
                    <option>Pizza Time</option>
                </select>

                <div class="search-box-invoice">
                    <input type="text" placeholder="Search invoice..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>

                <button class="filter-btn">
                    <i class="fa-solid fa-filter"></i> Filter
                </button>

                <button class="refresh-btn">
                    <i class="fa-solid fa-rotate-right"></i>
                </button>
            </div>
        </div>

        <div class="table-wrap">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Invoice No</th>
                        <th>Order Code</th>
                        <th>Branch</th>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>Invoice Date</th>
                        <th>Payment Method</th>
                        <th>Subtotal</th>
                        <th>Discount</th>
                        <th>Delivery Fee</th>
                        <th>Grand Total</th>
                        <th>Payment Status</th>
                        <th>Print</th>
                        <th style="width:50px;"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>1</td>
                        <td><a href="#" class="invoice-link">INV000121</a></td>
                        <td><a href="#" class="invoice-link">LDO001271</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Phouneta Sok</td>
                        <td>0969934999</td>
                        <td>10/04/2026 14:49</td>
                        <td>ABA</td>
                        <td class="amount">42.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="amount">42.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>2</td>
                        <td><a href="#" class="invoice-link">INV000122</a></td>
                        <td><a href="#" class="invoice-link">LDO001242</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Sok Vemean</td>
                        <td>086866554</td>
                        <td>10/04/2026 12:07</td>
                        <td>Cash</td>
                        <td class="amount">180.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="amount">180.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>3</td>
                        <td><a href="#" class="invoice-link">INV000123</a></td>
                        <td><a href="#" class="invoice-link">LDO001243</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Da Ra Pich</td>
                        <td>068717112</td>
                        <td>10/04/2026 12:06</td>
                        <td>ABA</td>
                        <td class="amount">308.00</td>
                        <td>10.00</td>
                        <td>0.00</td>
                        <td class="amount">298.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>4</td>
                        <td><a href="#" class="invoice-link">INV000124</a></td>
                        <td><a href="#" class="invoice-link">PTO00014</a></td>
                        <td>Pizza Time</td>
                        <td>Da Ra Pich</td>
                        <td>068717112</td>
                        <td>10/04/2026 14:16</td>
                        <td>Cash</td>
                        <td class="amount">8.99</td>
                        <td>0.00</td>
                        <td>1.00</td>
                        <td class="amount">9.99</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>5</td>
                        <td><a href="#" class="invoice-link">INV000125</a></td>
                        <td><a href="#" class="invoice-link">LDO001241</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Srey Neang</td>
                        <td>012321538</td>
                        <td>10/04/2026 12:07</td>
                        <td>ABA</td>
                        <td class="amount">288.00</td>
                        <td>0.00</td>
                        <td>2.00</td>
                        <td class="amount">290.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>6</td>
                        <td><a href="#" class="invoice-link">INV000126</a></td>
                        <td><a href="#" class="invoice-link">LMB000038</a></td>
                        <td>Little Meatbox</td>
                        <td>LD Peng Houth</td>
                        <td>000000003</td>
                        <td>10/04/2026 06:30</td>
                        <td>Cash</td>
                        <td class="amount">2.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="amount">2.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>7</td>
                        <td><a href="#" class="invoice-link">INV000127</a></td>
                        <td><a href="#" class="invoice-link">LDO001265</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Leng Kean</td>
                        <td>010618819</td>
                        <td>10/04/2026 15:04</td>
                        <td>ABA</td>
                        <td class="amount">27.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="amount">27.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>8</td>
                        <td><a href="#" class="invoice-link">INV000128</a></td>
                        <td><a href="#" class="invoice-link">LDO001244</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Tang Chhiv Hour</td>
                        <td>010965688</td>
                        <td>10/04/2026 12:07</td>
                        <td>ABA</td>
                        <td class="amount">128.00</td>
                        <td>0.00</td>
                        <td>1.50</td>
                        <td class="amount">129.50</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>9</td>
                        <td><a href="#" class="invoice-link">INV000129</a></td>
                        <td><a href="#" class="invoice-link">LMB000037</a></td>
                        <td>Little Meatbox</td>
                        <td>Kmall</td>
                        <td>000000002</td>
                        <td>10/04/2026 06:28</td>
                        <td>Cash</td>
                        <td class="amount">4.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="amount">4.00</td>
                        <td><span class="paid-badge">PAID</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>

                    <tr>
                        <td>10</td>
                        <td><a href="#" class="invoice-link">INV000130</a></td>
                        <td><a href="#" class="invoice-link">LDO001269</a></td>
                        <td>Little Duckling - SMC</td>
                        <td>Kreng Srey Sa</td>
                        <td>086889199</td>
                        <td>10/04/2026 12:08</td>
                        <td>ABA</td>
                        <td class="amount">159.00</td>
                        <td>0.00</td>
                        <td>0.00</td>
                        <td class="amount">159.00</td>
                        <td><span class="pending-badge">PENDING</span></td>
                        <td><button class="print-btn"><i class="fa-solid fa-print"></i></button></td>
                        <td><button class="dots-btn"><i class="fa-solid fa-ellipsis-vertical"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="footer-left">
                <span>Total of : 2</span>
                <span>Go to</span>
                <input type="text" value="1">
                <button class="go-btn">Go</button>
            </div>

            <div class="footer-right">
                <button class="page-btn"><i class="fa-solid fa-angle-left"></i></button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn"><i class="fa-solid fa-angle-right"></i></button>
            </div>
        </div>
    </div>
</main>

@include('layout.footer')