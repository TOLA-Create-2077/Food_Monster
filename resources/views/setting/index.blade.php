@php
    $pageTitle = 'System Settings';
    $currentPage = 'setting';
@endphp
@include('layout.header')
@include('layout.sidebar')

<main class="main">
    <div class="settings-container">
        <div class="settings-header">
            <h1>Settings</h1>
            <div class="settings-tabs">
                <button class="tab-btn active" data-tab="general">General</button>
                <button class="tab-btn" data-tab="printers">Printer List</button>
                <button class="tab-btn" data-tab="payment">Payment</button>
                <button class="tab-btn" data-tab="email">Email</button>
            </div>
        </div>

        {{-- General Tab --}}
        <div class="tab-pane active" id="tab-general">
            <form method="POST" action="{{ route('setting.update.general') }}" class="settings-form">
                @csrf
                <div class="form-group">
                    <label>Site Name</label>
                    <input type="text" name="site_name" value="{{ setting('site_name', 'Little Duckling') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Site Email</label>
                    <input type="email" name="site_email" value="{{ setting('site_email', 'admin@littleduckling.asia') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="site_phone" value="{{ setting('site_phone', '+855 12 345 678') }}" class="form-control">
                </div>
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>

        {{-- Printer List Tab --}}
        <div class="tab-pane" id="tab-printers">
            <div class="printers-header">
                <h3>Network Printers</h3>
                <button class="btn-primary" onclick="openPrinterModal()">+ Add Printer</button>
            </div>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th><th>Name</th><th>IP Address</th><th>Type</th><th>Bill Type</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="printersTableBody">
                        @foreach($printers as $printer)
                        <tr data-id="{{ $printer->id }}">
                            <td>{{ $printer->id }}</td>
                            <td>{{ $printer->name }}</td>
                            <td>{{ $printer->ip }}</td>
                            <td>{{ ucfirst($printer->printer_type ?? 'thermal') }}</td>
                            <td>{{ ucfirst($printer->bill_type ?? 'chef') }}</td>
                            <td><span class="badge {{ $printer->status == 'active' ? 'badge-success' : 'badge-danger' }}">{{ $printer->status }}</span></td>
                            <td>
                                <button class="btn-icon" onclick="editPrinter({{ $printer->id }})"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button class="btn-icon" onclick="deletePrinter({{ $printer->id }})"><i class="fa-regular fa-trash-can"></i></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Payment Tab --}}
        <div class="tab-pane" id="tab-payment">
            <form method="POST" action="{{ route('setting.update.payment') }}" class="settings-form">
                @csrf
                <div class="form-group">
                    <label>Default Currency</label>
                    <select name="currency" class="form-control">
                        <option value="USD" {{ setting('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="KHR" {{ setting('currency') == 'KHR' ? 'selected' : '' }}>KHR</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Exchange Rate (USD to KHR)</label>
                    <input type="number" step="0.01" name="exchange_rate" value="{{ setting('exchange_rate', 4100) }}" class="form-control">
                </div>
                <button type="submit" class="btn-save">Save</button>
            </form>
        </div>

        {{-- Email Tab --}}
        <div class="tab-pane" id="tab-email">
            <form method="POST" action="{{ route('setting.update.email') }}" class="settings-form">
                @csrf
                <div class="form-group">
                    <label>SMTP Host</label>
                    <input type="text" name="mail_host" value="{{ setting('mail_host', 'smtp.gmail.com') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>SMTP Port</label>
                    <input type="text" name="mail_port" value="{{ setting('mail_port', '587') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="mail_username" value="{{ setting('mail_username') }}" class="form-control">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="mail_password" value="" class="form-control" placeholder="Leave blank to keep current">
                </div>
                <div class="form-group">
                    <label>Encryption</label>
                    <select name="mail_encryption" class="form-control">
                        <option value="tls" {{ setting('mail_encryption') == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ setting('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
                <button type="submit" class="btn-save">Save</button>
            </form>
        </div>
    </div>
</main>

{{-- Printer Modal (Add/Edit) --}}
<div id="printerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Add Printer</h3>
            <span class="close" onclick="closePrinterModal()">&times;</span>
        </div>
        <form id="printerForm" method="POST">
            @csrf
            <input type="hidden" id="printer_id" name="printer_id">
            <div class="form-group">
                <label>Printer Name</label>
                <input type="text" id="printer_name" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>IP Address</label>
                <input type="text" id="printer_ip" name="ip" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Printer Type</label>
                <select id="printer_type" name="printer_type" class="form-control">
                    <option value="thermal">Thermal (ESC/POS)</option>
                    <option value="laser">Laser</option>
                    <option value="dotmatrix">Dot Matrix</option>
                </select>
            </div>
            <div class="form-group">
                <label>Bill Type</label>
                <select id="bill_type" name="bill_type" class="form-control">
                    <option value="customer">Customer Bill</option>
                    <option value="chef">Kitchen (Chef)</option>
                </select>
            </div>
            <div class="form-group">
                <label>Paper Size</label>
                <select id="paper_size" name="paper_size" class="form-control">
                    <option value="58mm">58mm</option>
                    <option value="80mm">80mm</option>
                </select>
            </div>
            <div class="form-group">
                <label>Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
            <div class="form-group">
                <label>Remark</label>
                <textarea id="remark" name="remark" class="form-control" rows="2"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closePrinterModal()">Cancel</button>
                <button type="submit" class="btn-primary">Save Printer</button>
            </div>
        </form>
    </div>
</div>

<style>
    /* Main layout */
    .main { flex: 1; padding: 24px 32px; background: #f5f7fb; }
    .settings-container { background: white; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden; }
    .settings-header { padding: 24px 28px 0 28px; border-bottom: 1px solid #eef2f6; }
    .settings-header h1 { font-size: 24px; margin-bottom: 16px; }
    .settings-tabs { display: flex; gap: 8px; }
    .tab-btn { background: none; border: none; padding: 10px 20px; font-size: 15px; font-weight: 500; color: #64748b; cursor: pointer; border-radius: 30px; transition: 0.2s; }
    .tab-btn.active { background: #eef2ff; color: #3b82f6; }
    .tab-pane { display: none; padding: 28px; }
    .tab-pane.active { display: block; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 6px; font-weight: 500; color: #1e293b; }
    .form-control { width: 100%; max-width: 400px; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font-size: 14px; }
    .btn-save, .btn-primary { background: #3b82f6; color: white; border: none; padding: 10px 24px; border-radius: 30px; cursor: pointer; font-weight: 500; }
    .btn-secondary { background: #e2e8f0; color: #1e293b; border: none; padding: 8px 20px; border-radius: 30px; cursor: pointer; }
    .printers-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th, .data-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #eef2f6; }
    .data-table th { background: #f8fafc; font-weight: 600; }
    .badge { padding: 4px 12px; border-radius: 40px; font-size: 12px; font-weight: 600; }
    .badge-success { background: #dcfce7; color: #15803d; }
    .badge-danger { background: #fee2e2; color: #b91c1c; }
    .btn-icon { background: none; border: none; font-size: 18px; cursor: pointer; margin: 0 4px; color: #5b6e8c; }
    .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; }
    .modal-content { background: white; border-radius: 20px; width: 500px; max-width: 90%; padding: 0; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 18px 24px; border-bottom: 1px solid #eef2f6; }
    .modal-header h3 { margin: 0; }
    .close { font-size: 28px; cursor: pointer; color: #94a3b8; }
    .modal-footer { padding: 16px 24px; border-top: 1px solid #eef2f6; display: flex; justify-content: flex-end; gap: 12px; }
</style>

<script>
    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
            document.getElementById(`tab-${btn.dataset.tab}`).classList.add('active');
        });
    });

    // Printer CRUD via AJAX
    function openPrinterModal(id = null) {
        const modal = document.getElementById('printerModal');
        const form = document.getElementById('printerForm');
        form.reset();
        document.getElementById('printer_id').value = '';
        document.getElementById('modalTitle').innerText = 'Add Printer';
        if (id) {
            fetch(`/setting/printer/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('printer_id').value = data.id;
                    document.getElementById('printer_name').value = data.name;
                    document.getElementById('printer_ip').value = data.ip;
                    document.getElementById('printer_type').value = data.printer_type || 'thermal';
                    document.getElementById('bill_type').value = data.bill_type || 'chef';
                    document.getElementById('paper_size').value = data.paper_size || '80mm';
                    document.getElementById('status').value = data.status || 'active';
                    document.getElementById('remark').value = data.remark || '';
                    document.getElementById('modalTitle').innerText = 'Edit Printer';
                });
        }
        modal.style.display = 'flex';
    }

    function closePrinterModal() {
        document.getElementById('printerModal').style.display = 'none';
    }

    document.getElementById('printerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('printer_id').value;
        const url = id ? `/setting/printer/${id}` : '/setting/printer';
        const method = id ? 'PUT' : 'POST';
        fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({
                name: document.getElementById('printer_name').value,
                ip: document.getElementById('printer_ip').value,
                printer_type: document.getElementById('printer_type').value,
                bill_type: document.getElementById('bill_type').value,
                paper_size: document.getElementById('paper_size').value,
                status: document.getElementById('status').value,
                remark: document.getElementById('remark').value,
            })
        }).then(res => res.json()).then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    });

    function editPrinter(id) { openPrinterModal(id); }

    function deletePrinter(id) {
        if (confirm('Delete this printer?')) {
            fetch(`/setting/printer/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            }).then(res => res.json()).then(data => {
                if (data.success) location.reload();
                else alert('Delete failed');
            });
        }
    }
</script>

@include('layout.footer')