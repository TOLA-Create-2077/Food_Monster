@php
    $pageTitle = "All Products";
    $currentPage = "customers_products";
@endphp

@include('layout.header')
@include('layout.sidebar')

<style>
.product-card{
    background: #fff;
    border-radius: 8px;
    padding: 16px;
    margin: 16px;
}

.product-topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:10px;
    margin-bottom:15px;
}

.product-title{
    font-weight:700;
    color:#4b5b73;
}

.product-actions{
    display:flex;
    gap:8px;
    flex-wrap:wrap;
}

.product-actions input{
    height:34px;
    border:1px solid #d8dee8;
    border-radius:6px;
    padding:0 10px;
}

.btn-blue{
    background:#4aa3df;
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:6px;
}

.btn-red{
    background:#ff6b6b;
    color:#fff;
    border:none;
    padding:6px 12px;
    border-radius:6px;
}

.table-wrap{
    overflow-x:auto;
}

.product-table{
    width:100%;
    border-collapse:collapse;
    min-width:1200px;
}

.product-table th,
.product-table td{
    padding:12px;
    border-bottom:1px solid #edf1f5;
    font-size:14px;
}

.product-table th{
    background:#fafbfd;
}

.status-active{
    background:#daf5dc;
    color:#28a745;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

.status-inactive{
    background:#ffdede;
    color:#dc3545;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}
</style>

<main class="main">

<div class="product-card">

    <!-- TOP BAR -->
    <div class="product-topbar">
        <div class="product-title">All Items : 286</div>

        <div class="product-actions">
            <input type="text" placeholder="Search..." />
            <button class="btn-blue"><i class="fa fa-filter"></i> Filter</button>
            <button class="btn-blue"><i class="fa fa-plus"></i> Create New</button>
            <button class="btn-red">Trash</button>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-wrap">
        <table class="product-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Code</th>
                    <th>Title (EN)</th>
                    <th>Title (KM)</th>
                    <th>Type</th>
                    <th>Unit</th>
                    <th>Description</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>1</td>
                    <td>FSC-CS0003</td>
                    <td>Chicken Rosemary + Beef Wellington</td>
                    <td>មាន់រ៉ូសម៉ារី + សាច់គោ</td>
                    <td>POS_CUSTOMER</td>
                    <td>Set</td>
                    <td>Chicken Rosemary, Beef Wellington</td>
                    <td><span class="status-active">Active</span></td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>FSC-D-SET017</td>
                    <td>Chicken Ballotine</td>
                    <td>មាន់បាលូទីន</td>
                    <td>POS</td>
                    <td>Pieces</td>
                    <td>Chicken Ballotine</td>
                    <td><span class="status-active">Active</span></td>
                </tr>

                <tr>
                    <td>3</td>
                    <td>FSC-S-SET016</td>
                    <td>Chicken Rosemary (SET)</td>
                    <td>មាន់រ៉ូសម៉ារី</td>
                    <td>POS</td>
                    <td>Pieces</td>
                    <td>Chicken Rosemary</td>
                    <td><span class="status-active">Active</span></td>
                </tr>

                <tr>
                    <td>4</td>
                    <td>FSC-S-SET015</td>
                    <td>Beijing Duck (SET)</td>
                    <td>ទាខ្ចីប៉េកាំង</td>
                    <td>POS</td>
                    <td>Set</td>
                    <td>Beijing Duck</td>
                    <td><span class="status-active">Active</span></td>
                </tr>

                <tr>
                    <td>5</td>
                    <td>FSC-S-G0001</td>
                    <td>Goose Set</td>
                    <td>ឈុតពងទា</td>
                    <td>POS_CUSTOMER</td>
                    <td>Set</td>
                    <td>Special Goose Menu</td>
                    <td><span class="status-active">Active</span></td>
                </tr>

                <tr>
                    <td>6</td>
                    <td>FSC-S-D0005</td>
                    <td>Whole Beijing Duck</td>
                    <td>ទាខ្ចីទាំងមូល</td>
                    <td>POS_CUSTOMER</td>
                    <td>Set</td>
                    <td>Whole Duck</td>
                    <td><span class="status-active">Active</span></td>
                </tr>

                <tr>
                    <td>7</td>
                    <td>FSC-S-PG005</td>
                    <td>Roasted Pig 2.7kg</td>
                    <td>ជ្រូកអាំង</td>
                    <td>POS_CUSTOMER</td>
                    <td>Set</td>
                    <td>Roasted Pig</td>
                    <td><span class="status-inactive">Inactive</span></td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

</main>

@include('layout.footer')