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
    <title>Dashboard Admin — TechMonitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
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

        #barChart {
    height: 180px; 
    padding-bottom: 25px; /* Beri ruang untuk label hari */
    display: flex;
    align-items: flex-end;
    gap: 10px;
}

.bar-wrap {
    flex: 1;
    height: 100%; /* WAJIB: Agar batang bisa mengambil % dari tinggi ini */
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
}

.bar {
    width: 80%;
    min-height: 4px; /* Agar tetap terlihat meski data 0 */
    border-radius: 4px 4px 0 0;
    background: var(--navy-4);
    transition: height 0.6s cubic-bezier(0.4, 0, 0.2, 1);
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

        .chart-area {
    padding: 20px;
    display: flex;
    align-items: flex-end; /* PENTING: Agar batang menempel di bawah */
    gap: 8px;
    height: 160px; /* Tentukan tinggi pasti */
}

.bar {
    width: 100%;
    border-radius: 4px 4px 0 0;
    transition: height 0.5s ease-in-out; /* Animasi real-time */
}
    </style>
</head>
<body>

<!-- ── Sidebar ─────────────────────────────────────────────────────────── -->
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">TechMonitor</div>
        <div class="brand-sub">PT. Nusantara CCTV</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu Utama</div>
        <a href="admin_dashboard.php" class="nav-item active">
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
                <div class="user-name"><?= htmlspecialchars($namaAdmin) ?></div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<!-- ── Main ────────────────────────────────────────────────────────────── -->
<main class="main">
    <div class="topbar">
        <div>
            <div class="topbar-title" style="font-weight: 700;">Dashboard Admin</div>
            <div class="topbar-date" id="currentDate" style="font-size: 0.8rem; color: var(--text-muted);"></div>
        </div>
        <form method="POST" action="../../../app/controllers/AuthController.php">
            <button type="submit" name="logout" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Keluar</button>
        </form>
    </div>

    <div class="content">
        <div class="stats-grid">
            <div class="stat-card blue">
                <div class="stat-icon blue"><i class="bi bi-people-fill"></i></div>
                <div class="stat-value" id="val-teknisi">0</div>
                <div class="stat-label">Total Teknisi</div>
            </div>
            <div class="stat-card yellow">
                <div class="stat-icon yellow"><i class="bi bi-clipboard2-pulse-fill"></i></div>
                <div class="stat-value" id="val-hari-ini">0</div>
                <div class="stat-label">Tiket Hari Ini</div>
            </div>
            <div class="stat-card green">
                <div class="stat-icon green"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-value" id="val-selesai">0</div>
                <div class="stat-label">Tiket Selesai</div>
            </div>
            <div class="stat-card red">
                <div class="stat-icon red"><i class="bi bi-exclamation-triangle-fill"></i></div>
                <div class="stat-value" id="val-pending">0</div>
                <div class="stat-label">Tiket Pending</div>
            </div>
        </div>

        <div class="dashboard-grid">
            <div>
                <div class="panel">
                    <div class="panel-header"><span class="panel-title">Volume Tiket Teratasi (7 Hari Terakhir)</span></div>
                    <div class="chart-area" id="barChart"></div>
                </div>
                <div class="panel">
                    <div class="panel-header">
                        <span class="panel-title">Detail Aktivitas (Live)</span>
                        <span class="panel-badge" id="total-entri">0 entri</span>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID Tiket</th>
                                    <th>Teknisi</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="table-aktivitas"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div>
                <div class="panel">
                    <div class="panel-header"><span class="panel-title">Aktivitas Cepat</span></div>
                    <div style="padding:20px;" id="recent-activity-list"></div>
                </div>
                <div class="panel">
                    <div class="panel-header"><span class="panel-title">Kinerja Teknisi</span></div>
                    <div id="kinerja-teknisi-list"></div>
                </div>
                <div class="panel">
                    <div class="panel-header"><span class="panel-title">Ringkasan Status</span></div>
                    <div id="ringkasan-status-list" style="padding:20px; display:flex; flex-direction:column; gap:12px;"></div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   function updateRealtime() {
        const fetchPath = '../../../app/fetch_data.php';

        // 1. Ambil Statistik Kartu Utama
        $.getJSON(`${fetchPath}?action=get_stats`, function(data) {
            $('#val-teknisi').text(data.total_teknisi);
            $('#val-pending').text(data.tiket_pending);
            $('#val-selesai').text(data.tiket_selesai);
            $('#val-hari-ini').text(data.tiket_hari_ini);
        });

        // 2. Ambil Aktivitas & Tabel (Live)
        $.getJSON(`${fetchPath}?action=get_aktivitas`, function(data) {
            let listHtml = ''; let tableHtml = '';
            data.forEach(item => {
                const color = item.status === 'selesai' ? 'success' : (item.status === 'proses' ? 'warning' : 'danger');
                listHtml += `
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon ${color}" style="width:30px;height:30px;margin-bottom:0;margin-right:12px;font-size:0.8rem;">
                            <i class="bi bi-${item.status === 'selesai' ? 'check-lg' : 'clock'}"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 text-white" style="font-size:0.8rem;">${item.judul}</h6>
                            <small class="text-muted" style="font-size:0.65rem;">${item.jam} WIB</small>
                        </div>
                    </div>`;
                tableHtml += `
                    <tr>
                        <td><span class="ticket-id">${item.id_tiket}</span></td>
                        <td class="fw-bold">${item.nama_teknisi}</td>
                        <td>${item.lokasi}</td>
                        <td><span class="badge-status ${color}">${item.status.toUpperCase()}</span></td>
                        <td><small class="text-muted">${item.tgl_full}</small></td>
                    </tr>`;
            });
            $('#recent-activity-list').html(listHtml);
            $('#table-aktivitas').html(tableHtml);
            $('#total-entri').text(data.length + " entri terbaru");
        });

        // 3. Ambil Data Grafik, Kinerja, dan Ringkasan (KOREKSI DI SINI)
        $.getJSON(`${fetchPath}?action=get_dashboard_all`, function(data) {
            // RENDER GRAFIK
            const chartData = data.grafik;
            const values = chartData.map(g => parseInt(g.val));
            const maxVal = Math.max(...values) || 1;

            let chartHtml = '';
            chartData.forEach(g => {
                const heightPct = (parseInt(g.val) / maxVal) * 100;
                const barColor = g.val > 0 ? 'var(--accent)' : 'var(--navy-4)';

                chartHtml += `
                    <div class="bar-wrap">
                        <div class="bar" 
                             style="height: ${heightPct}%; background-color: ${barColor};" 
                             title="${g.val} Tiket Selesai">
                        </div>
                        <div class="bar-label" style="margin-top: 8px; font-size: 0.65rem; color: var(--text-muted);">
                            ${g.day}
                        </div>
                    </div>`;
            });
            $('#barChart').html(chartHtml);

            // RENDER KINERJA TEKNISI
            let tekHtml = '';
            data.teknisi.forEach(t => {
                const pct = t.tugas > 0 ? Math.round((t.selesai / t.tugas) * 100) : 0;
                tekHtml += `
                    <div class="teknisi-item">
                        <div class="avatar-sm">${t.foto}</div>
                        <div style="flex:1;">
                            <div class="teknisi-name" style="font-size:0.8rem;">${t.nama}</div>
                            <div class="progress-mini"><div class="progress-mini-bar" style="width:${pct}%"></div></div>
                        </div>
                        <div style="font-size:.7rem; color:var(--accent-2);">${pct}%</div>
                    </div>`;
            });
            $('#kinerja-teknisi-list').html(tekHtml);

            // RENDER RINGKASAN STATUS
            let ringHtml = '';
            data.ringkasan.forEach(r => {
                const pct = Math.round((r.val / r.total) * 100);
                ringHtml += `
                    <div>
                        <div class="d-flex justify-content-between" style="font-size:.75rem; margin-bottom:4px;">
                            <span>${r.label}</span><span>${r.val} Tiket</span>
                        </div>
                        <div style="height:4px; background:var(--navy-3); border-radius:2px; overflow:hidden;">
                            <div style="height:100%; width:${pct}%; background:${r.color};"></div>
                        </div>
                    </div>`;
            });
            $('#ringkasan-status-list').html(ringHtml);
        });
    }

    $(document).ready(function() {
        updateRealtime();
        setInterval(updateRealtime, 3000); 
        $('#currentDate').text(new Date().toLocaleDateString('id-ID', { 
            weekday:'long', year:'numeric', month:'long', day:'numeric' 
        }));
    });
</script>
</body>
</html>
