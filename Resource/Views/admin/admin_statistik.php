<?php
session_start();

// Guard: hanya admin yang boleh akses
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
    <title>Statistik Laporan — TechMonitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:       #0b1120;
            --navy-2:     #111827;
            --navy-3:     #1e2d45;
            --accent-2:   #60a5fa;
            --text:       #e2e8f0;
            --text-white: #64748b;
            --border:     rgba(255,255,255,0.07);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            margin: 0;
            display: flex;
        }

        /* Sidebar */
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

        .sidebar-brand { padding: 24px 20px 20px; border-bottom: 1px solid var(--border); }
        .brand-name { font-size: 1.2rem; font-weight: 800; color: var(--accent-2); letter-spacing: -0.5px; }
        .brand-sub { font-size: 0.68rem; color: var(--text-white); text-transform: uppercase; letter-spacing: 1px; }

        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-label { font-size: 0.65rem; color: var(--text-white); text-transform: uppercase; letter-spacing: 1.5px; padding: 8px 8px 4px; margin-top: 8px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px;
            color: var(--text-white); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all .2s; margin-bottom: 2px;
        }

        .nav-item:hover, .nav-item.active { background: var(--navy-3); color: var(--text); }
        .nav-item.active { color: var(--accent-2); }
        .nav-item i { font-size: 1rem; width: 18px; }

        .sidebar-footer { padding: 16px; border-top: 1px solid var(--border); }
        .user-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; background: var(--navy-3); }
        .avatar { width: 34px; height: 34px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: white; }
        .user-name { font-size: 0.8rem; font-weight: 600; color: white; }
        .user-role { font-size: 0.68rem; color: var(--text-white); }

        /* Main Layout */
        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { padding: 18px 28px; border-bottom: 1px solid var(--border); background: var(--navy-2); display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 50; }
        .content { padding: 28px; }

        /* Cards */
        .report-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .report-card { background: var(--navy-2); border: 1px solid var(--border); border-radius: 14px; padding: 20px; }
        .report-val { font-size: 1.5rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: var(--accent-2); }
        .report-label { font-size: 0.75rem; color: var(--text-white); text-transform: uppercase; letter-spacing: 0.5px; }

        /* Panel Grafik */
        .panel { background: var(--navy-2); border: 1px solid var(--border); border-radius: 14px; padding: 24px; }
        .chart-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .bar-container { 
    display: flex; 
    align-items: flex-end; 
    gap: 8px; /* Diperkecil dari 12px */
    height: 200px; 
    padding-bottom: 20px; 
    border-bottom: 1px solid var(--border); 
}
        .bar-wrapper { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .bar-fill { width: 100%; background: var(--navy-3); border-radius: 6px 6px 0 0; transition: 0.3s; }
        .bar-fill:hover { background: var(--accent-2); }
        .bar-label { font-size: 0.7rem; color: var(--text-white); }

        

        .sidebar-footer { padding: 16px; border-top: 1px solid var(--border); }
        .user-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; background: var(--navy-3); }
        .avatar { width: 34px; height: 34px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: white; }

        @media print {
            .sidebar, .topbar, .no-print, button, select { display: none !important; }
            .main { margin-left: 0 !important; width: 100% !important; }
            body { background: white !important; color: black !important; }
            .report-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .report-card { border: 1px solid #ddd !important; background: #f9f9f9 !important; color: black !important; -webkit-print-color-adjust: exact; }
            .report-val { color: #0b1120 !important; }
            .panel { border: 1px solid #ddd !important; break-inside: avoid; }
            .bar-fill { background: #3b82f6 !important; -webkit-print-color-adjust: exact; }
        }
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
        <a href="admin_dashboard.php" class="nav-item">
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
        <a href="admin_statistik.php" class="nav-item active">
            <i class="bi bi-bar-chart-fill"></i> Statistik
        </a>
        <a href="admin_export.php" class="nav-item">
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
                <div class="user-name">Administrator</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<main class="main">
    <div class="topbar no-print">
        <div style="font-weight: 700;">Statistik & Laporan Kinerja</div>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-2"></i> Cetak ke PDF
        </button>
    </div>

    <div class="content">
        <div class="report-card">
        <div class="report-label">Selesai Bulan Ini</div>
        <div class="report-val" id="stat-total">0</div>
    </div>
    <div class="report-card" style="border-left: 4px solid var(--accent-2);">
        <div class="report-label">Selesai Hari Ini</div>
        <div class="report-val" id="stat-today" style="color: #4ade80;">0</div>
    </div>
    <div class="report-card">
        <div class="report-label">Teknisi Terbaik</div>
        <div id="stat-best" style="font-size: 1rem; font-weight: 700; color: #60a5fa; margin-top: 5px;">Memuat...</div>
    </div>
</div>

        <div class="panel">
            <div class="chart-header">
                <span style="font-weight: 700; font-size: 0.9rem;">Volume Tiket 7 Hari Terakhir</span>
                <select class="form-select-sm bg-transparent text-white border-secondary border-opacity-25 no-print" style="font-size: 0.75rem;">
                    <option>April 2026</option>
                </select>
            </div>
            <div class="bar-container" id="chart-mingguan">
                </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadStatistik() {
    $.ajax({
        url: '../../../app/fetch_data.php?action=get_statistik_lengkap',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            // 1. Update Kartu Statistik Angka
            $('#stat-total').text(data.total_tiket);
            $('#stat-today').text(data.tiket_hari_ini);
            $('#stat-best').text(data.best_teknisi);

            // 2. Render Diagram Batang (7 Hari Terakhir)
            let barHtml = '';
            
            // Mencari nilai tertinggi untuk skala tinggi diagram
            const values = data.mingguan.map(d => parseInt(d.v));
            const maxVal = Math.max(...values) || 1; 
            
            data.mingguan.forEach(item => {
                // Hitung tinggi batang dalam persen
                const height = (item.v / maxVal) * 100;
                
                // Gunakan inline style flexbox agar batang menempel di bawah
                barHtml += `
                <div class="bar-wrapper" style="flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: flex-end; height: 100%;">
                    <div class="bar-fill" 
                         style="width: 60%; 
                                height: ${item.v > 0 ? height : 2}%; 
                                background: ${item.v > 0 ? 'var(--accent-2)' : 'var(--navy-3)'}; 
                                border-radius: 4px 4px 0 0; 
                                transition: height 0.8s ease-in-out; 
                                cursor: pointer;" 
                         title="${item.v} Tiket">
                    </div>
                    <div class="bar-label" style="font-size: 0.65rem; color: var(--text-white); margin-top: 10px; white-space: nowrap;">
                        ${item.m}
                    </div>
                </div>`;
            });
            
            // Masukkan ke container grafik
            $('#chart-mingguan').html(barHtml);
        },
        error: function(xhr, status, error) {
            console.error("Gagal memperbarui statistik:", error);
        }
    });
}

// Inisialisasi saat halaman selesai dimuat
$(document).ready(function() {
    // Jalankan sekali saat start
    loadStatistik();
    
    // Set interval untuk update otomatis setiap 5 detik (Real-time)
    setInterval(loadStatistik, 5000);
});
</script>

</body>
</html>