@php
    $pageTitle = "Dashboard";
    $currentPage = "dashboard";
@endphp

@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="filters">
        <input class="date-input" type="text" value="{{ now()->format('d/m/Y') }}" />
        <input class="date-input" type="text" value="{{ now()->format('d/m/Y') }}" />
        <button class="icon-btn search" aria-label="Search">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        <button class="icon-btn refresh" aria-label="Refresh">
            <i class="fa-solid fa-rotate-right"></i>
        </button>
    </div>

    <section class="stats-grid">
        <div class="stat-card">
            <div class="label">All Orders</div>
            <div class="value-row">
                <div class="value">{{ $allOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Pending Orders</div>
            <div class="value-row">
                <div class="value">{{ $pendingOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Confirmed Orders</div>
            <div class="value-row">
                <div class="value">{{ $confirmedOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Rejected Orders</div>
            <div class="value-row">
                <div class="value">{{ $rejectedOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Cancelled Orders</div>
            <div class="value-row">
                <div class="value">{{ $cancelledOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Cooking Started Orders</div>
            <div class="value-row">
                <div class="value">{{ $cookingStartedOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Cooking Almost Finished Orders</div>
            <div class="value-row">
                <div class="value">{{ $cookingAlmostFinishedOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Food Ready</div>
            <div class="value-row">
                <div class="value">{{ $foodReadyOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Delivery Started Orders</div>
            <div class="value-row">
                <div class="value">{{ $deliveryStartedOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">Finished Orders</div>
            <div class="value-row">
                <div class="value">{{ $finishedOrders }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">User Admin</div>
            <div class="value-row">
                <div class="value">{{ $userAdmin }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">User Driver</div>
            <div class="value-row">
                <div class="value">{{ $userDriver }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">POS Admin</div>
            <div class="value-row">
                <div class="value">{{ $posAdmin }}</div>
                <div class="total">Total</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="label">User Customer</div>
            <div class="value-row">
                <div class="value">{{ $userCustomer }}</div>
                <div class="total">Total</div>
            </div>
        </div>
    </section>

    <section class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">Orders by Status</div>
            <div class="chart-wrap">
                <canvas id="ordersStatusChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">Orders by Source</div>
            <div class="chart-wrap">
                <canvas id="ordersSourceChart"></canvas>
            </div>
        </div>

        <div class="chart-card">
            <div class="chart-title">Payments by Status (Amount)</div>
            <div class="chart-wrap">
                <canvas id="paymentsChart"></canvas>
            </div>
        </div>
    </section>

    <section class="wide-card">
        <div class="chart-title">Revenue as $</div>
        <div class="revenue-wrap">
            <canvas id="revenueChart"></canvas>
        </div>
    </section>
</main>

<script>
    Chart.defaults.font.family = "Arial, Helvetica, sans-serif";
    Chart.defaults.color = "#6b7688";

    function buildHorizontalBarChart(canvasId, label, value, color, maxValue) {
        const ctx = document.getElementById(canvasId);

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: [label],
                datasets: [{
                    data: [value],
                    backgroundColor: color,
                    borderRadius: 0,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: "y",
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        suggestedMax: maxValue,
                        grid: { color: "#edf1f7" },
                        border: { display: false },
                        ticks: { stepSize: 10 }
                    },
                    y: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { display: false }
                    }
                }
            }
        });
    }

    buildHorizontalBarChart("ordersStatusChart", "Orders", {{ $allOrders }}, "#5f92e6", {{ max($allOrders + 10, 10) }});
    buildHorizontalBarChart("paymentsChart", "Paid", {{ $paidAmount }}, "#58b782", {{ max($paidAmount + 100, 100) }});

    new Chart(document.getElementById("ordersSourceChart"), {
        type: "bar",
        data: {
            labels: {!! json_encode($ordersBySource->pluck('order_source')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($ordersBySource->pluck('total')->toArray()) !!},
                backgroundColor: "#62b2da",
                borderRadius: 0,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 5 },
                    grid: { color: "#edf1f7" },
                    border: { display: false }
                }
            }
        }
    });

    new Chart(document.getElementById("revenueChart"), {
        type: "line",
        data: {
            labels: {!! json_encode($revenueLabels) !!},
            datasets: [
                {
                    label: "After Discount",
                    data: {!! json_encode($afterDiscountData) !!},
                    borderColor: "#66b6e2",
                    backgroundColor: "rgba(102, 182, 226, 0.15)",
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3
                },
                {
                    label: "Grand Total",
                    data: {!! json_encode($grandTotalData) !!},
                    borderColor: "#8a8a8a",
                    backgroundColor: "rgba(138, 138, 138, 0.08)",
                    fill: false,
                    tension: 0.35,
                    pointRadius: 3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: "top",
                    labels: {
                        boxWidth: 28
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    border: { display: false }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback(value) {
                            return "$" + Number(value).toLocaleString();
                        }
                    },
                    grid: { color: "#edf1f7" },
                    border: { display: false }
                }
            }
        }
    });
</script>

@include('layout.footer') i need all this show from my database please  this is from my database structure that you give me   can it do??