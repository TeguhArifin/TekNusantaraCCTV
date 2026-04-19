<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}
$namaAdmin = $_SESSION['nama_lengkap'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Laporan — TechMonitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
:root {
            --navy:       #0b1120;
            --navy-2:     #111827;
            --navy-3:     #1e2d45;
            --navy-4:     #253554;
            --accent:     #3b82f6;
            --accent-2:   #60a5fa;
            --success:    #22c55e;
            --warning:    #f59e0b;
            --danger:     #ef4444;
            --text:       #e2e8f0;
            --text-muted: #64748b;
            --border:     rgba(255,255,255,0.07);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            min-height: 100vh;
            display: flex;
        }

        /* ── Sidebar ────────────────────────────────── */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--navy-2);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 24px 20px 20px;
            border-bottom: 1px solid var(--border);
        }

        .brand-name {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--accent-2);
            letter-spacing: -0.5px;
        }

        .brand-sub {
            font-size: 0.68rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-nav { padding: 16px 12px; flex: 1; }

        .nav-label {
            font-size: 0.65rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 8px 8px 4px;
            margin-top: 8px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all .2s;
            margin-bottom: 2px;
        }

        .nav-item:hover, .nav-item.active {
            background: var(--navy-3);
            color: var(--text);
        }

        .nav-item.active { color: var(--accent-2); }
        .nav-item i { font-size: 1rem; width: 18px; }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            background: var(--navy-3);
        }

        .avatar {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.75rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name { font-size: 0.8rem; font-weight: 600; }
        .user-role { font-size: 0.68rem; color: var(--text-muted); }

        /* ── Main ───────────────────────────────────── */
        .main {
            margin-left: 240px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            padding: 18px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--navy-2);
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title { font-size: 1rem; font-weight: 700; }
        .topbar-date { font-size: 0.8rem; color: var(--text-muted); }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--text-muted);
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-family: inherit;
            cursor: pointer;
            transition: all .2s;
        }

        .logout-btn:hover { border-color: var(--danger); color: var(--danger); }

        .content { padding: 28px; flex: 1; }

        /* ── Stats Cards ────────────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-card {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            transition: border-color .2s;
        }

        .stat-card:hover { border-color: rgba(59,130,246,0.3); }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; right: 0;
            width: 80px; height: 80px;
            border-radius: 50%;
            opacity: .06;
            transform: translate(20px, -20px);
        }

        .stat-card.blue::before   { background: #3b82f6; }
        .stat-card.green::before  { background: #22c55e; }
        .stat-card.yellow::before { background: #f59e0b; }
        .stat-card.red::before    { background: #ef4444; }

        .stat-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            margin-bottom: 14px;
        }

        .stat-icon.blue   { background: rgba(59,130,246,0.15); color: #60a5fa; }
        .stat-icon.green  { background: rgba(34,197,94,0.15);  color: #4ade80; }
        .stat-icon.yellow { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .stat-icon.red    { background: rgba(239,68,68,0.15);  color: #f87171; }

        .stat-value {
            font-size: 2rem;
            font-weight: 800;
            font-family: 'JetBrains Mono', monospace;
            line-height: 1;
            margin-bottom: 4px;
        }

        .stat-label { font-size: 0.78rem; color: var(--text-muted); }

        .stat-change {
            font-size: 0.72rem;
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .stat-change.up   { color: var(--success); }
        .stat-change.down { color: var(--danger); }

        /* ── Grid Layout ────────────────────────────── */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 20px;
        }

        /* ── Panel ──────────────────────────────────── */
        .panel {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-title { font-size: 0.875rem; font-weight: 700; }

        .panel-badge {
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--navy-3);
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* ── Table ──────────────────────────────────── */
        .data-table { width: 100%; border-collapse: collapse; }

        .data-table th {
            padding: 10px 16px;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-muted);
            text-align: left;
            border-bottom: 1px solid var(--border);
            font-weight: 600;
        }

        .data-table td {
            padding: 12px 16px;
            font-size: 0.825rem;
            border-bottom: 1px solid var(--border);
        }

        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: rgba(255,255,255,0.02); }

        .ticket-id {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--accent-2);
        }

        .badge-status {
            display: inline-block;
            font-size: 0.7rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
        }

        .badge-status.success { background: rgba(34,197,94,0.15);  color: #4ade80; }
        .badge-status.warning { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-status.danger  { background: rgba(239,68,68,0.15);  color: #f87171; }

        /* ── Teknisi Card ───────────────────────────── */
        .teknisi-item {
            padding: 14px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .teknisi-item:last-child { border-bottom: none; }

        .avatar-sm {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: var(--navy-4);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--accent-2);
            flex-shrink: 0;
        }

        .teknisi-name { font-size: 0.825rem; font-weight: 600; }

        .progress-mini {
            width: 100%;
            height: 4px;
            background: var(--navy-3);
            border-radius: 4px;
            margin-top: 5px;
            overflow: hidden;
        }

        .progress-mini-bar {
            height: 100%;
            border-radius: 4px;
            background: var(--accent);
            transition: width .6s ease;
        }

        .teknisi-meta { font-size: 0.68rem; color: var(--text-muted); }

        /* ── Bar Chart Dummy ────────────────────────── */
        .chart-area {
            padding: 20px;
            display: flex;
            align-items: flex-end;
            gap: 8px;
            height: 140px;
        }

        .bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; }

        .bar {
            width: 100%;
            border-radius: 6px 6px 0 0;
            background: var(--navy-4);
            position: relative;
            transition: background .2s;
            min-height: 4px;
        }

        .bar:hover { background: var(--accent); }

        .bar-label { font-size: 0.62rem; color: var(--text-muted); }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .stat-card { animation: fadeUp .4s ease both; }
        .stat-card:nth-child(1) { animation-delay: .05s; }
        .stat-card:nth-child(2) { animation-delay: .10s; }
        .stat-card:nth-child(3) { animation-delay: .15s; }
        .stat-card:nth-child(4) { animation-delay: .20s; }

        :root {
        --navy: #0b1120;
        --navy-2: #111827;
        --navy-3: #1e2d45;
        --accent: #3b82f6;
        --accent-2: #60a5fa;
        --text-main: #f8fafc;
        --border: rgba(255,255,255,0.07);
    }

    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background: var(--navy);
        color: var(--text-main);
        margin: 0;
        display: flex; /* Pastikan flex aktif agar sidebar dan main sejajar */
    }

    /* Area Utama: Dibuat lebar maksimal */
    .main {
        margin-left: 240px; /* Lebar sidebar */
        flex: 1; 
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        padding: 40px;
    }

    /* Card Export: Dibuat lebar mengikuti space */
    .export-area {
        background: var(--navy-2);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 40px;
        width: 100%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    /* Form & Input */
    .form-label { color: var(--accent-2); font-weight: 600; margin-bottom: 10px; }
    .form-control { 
        background: var(--navy-3) !important; 
        border: 1px solid var(--border) !important; 
        color: white !important; 
        padding: 12px;
        font-size: 1rem;
    }

    /* Tabel Custom */
    .table-custom { color: white !important; margin-top: 20px; }
    .table-custom th { 
        background: var(--navy-3) !important; 
        color: var(--accent-2) !important; 
        border-bottom: 2px solid var(--accent) !important;
        padding: 15px;
    }
    .table-custom td { padding: 15px; border-bottom: 1px solid var(--border); }

    /* CSS Khusus Print (PDF) */
    @media print {
        .sidebar, .no-print, .btn, hr { display: none !important; }
        .main { margin: 0 !important; padding: 0 !important; width: 100% !important; }
        body { background: white !important; color: black !important; }
        .export-area { background: white !important; border: none !important; padding: 0 !important; }
        .table-custom { width: 100% !important; color: black !important; border: 1px solid #000 !important; }
        .table-custom th, .table-custom td { border: 1px solid #000 !important; color: black !important; background: transparent !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; color: black !important; }
    }
    .print-header { display: none; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">TechMonitor</div>
        <div class="brand-sub">PT. Nusantara CCTV</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>
        <a href="admin_dashboard.php" class="nav-item ">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="admin_tiket.php" class="nav-item">
            <i class="bi bi-ticket-detailed"></i> Tiket Kerja
        </a>
        <a href="admin_teknisi.php" class="nav-item">
            <i class="bi bi-people-fill"></i> Data Teknisi
        </a>
        <a href="admin_lokasi.php" class="nav-item">
            <i class="bi bi-geo-alt-fill"></i> Lokasi
        </a>

        <div class="nav-label">Laporan</div>
        <a href="admin_statistik.php" class="nav-item">
            <i class="bi bi-bar-chart-fill"></i> Statistik
        </a>
        <a href="admin_export.php" class="nav-item active">
            <i class="bi bi-file-earmark-text"></i> Ekspor Data
        </a>

        <div class="nav-label">Sistem</div>
        <a href="admin_pengaturan.php" class="nav-item">
            <i class="bi bi-gear-fill"></i> Pengaturan
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="avatar">AD</div>
            <div>
                <div class="user-name"><?= htmlspecialchars($namaAdmin) ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<div class="main">
    <div class="export-container"> <div class="export-area" id="area-laporan">
            
            <div class="print-header">
                <h2 style="margin:0; color: black;">LAPORAN KERJA TEKNISI</h2>
                <p style="color: #666;">PT. NUSA CCTV - Cibinong, Bogor</p>
                <hr style="border: 1px solid #000;">
            </div>

            <h4 class="mb-4 no-print"><i class="bi bi-download me-2"></i>Export Laporan</h4>

            <table class="table table-custom" id="table-ke-excel">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Teknisi</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="exportBody">
                    </tbody>
            </table>

            <div class="d-flex gap-2 mt-4 no-print">
                <button onclick="window.print()" class="btn btn-primary px-4">
                    <i class="bi bi-file-pdf"></i> Simpan ke PDF
                </button>
                <button onclick="unduhExcel()" class="btn btn-success px-4">
                    <i class="bi bi-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Fungsi untuk memuat laporan hari ini secara otomatis
function loadLaporanRealtime() {
    // Memanggil action baru dari fetch_data.php
    $.getJSON('../../../app/fetch_data.php?action=get_laporan_hari_ini', function(data) {
        renderTable(data);
    }).fail(function() {
        console.error("Gagal mengambil data laporan.");
    });
}

// Fungsi untuk merender baris tabel
function renderTable(data) {
    let rows = '';
    if (!data || data.length === 0) {
        rows = '<tr><td colspan="4" class="text-center text-muted">Belum ada aktivitas hari ini.</td></tr>';
    } else {
        const badges = { 
            'selesai': 'success', 
            'proses': 'warning', 
            'pending': 'danger' 
        };
        
        data.forEach(item => {
            // Ambil jam saja dari format YYYY-MM-DD HH:MM:SS
            let jam = item.waktu ? item.waktu.split(' ')[1].substring(0, 5) : '--:--';
            
            rows += `<tr>
                <td style="font-family:'JetBrains Mono'">${jam} WIB</td>
                <td class="fw-bold">${item.teknisi}</td>
                <td>${item.lokasi}</td>
                <td>
                    <span class="badge bg-${badges[item.status.toLowerCase()] || 'secondary'}">
                        ${item.status.toUpperCase()}
                    </span>
                </td>
            </tr>`;
        });
    }
    $('#exportBody').html(rows);
}

// Fungsi Export Excel sederhana
function unduhExcel() {
    let table = document.getElementById("table-ke-excel");
    let html = table.outerHTML;
    window.open('data:application/vnd.ms-excel,' + encodeURIComponent(html));
}

$(document).ready(function() {
    // Load data saat halaman dibuka
    loadLaporanRealtime();

    // Update otomatis setiap 5 detik (Real-time)
    setInterval(loadLaporanRealtime, 5000);
});
</script>
</body>
</html>