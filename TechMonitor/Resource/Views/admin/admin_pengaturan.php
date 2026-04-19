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
    <title>Pengaturan Sistem — TechMonitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:       #0b1120;
            --navy-2:     #111827;
            --navy-3:     #1e2d45;
            --accent:     #3b82f6;
            --accent-2:   #60a5fa;
            --text:       #e2e8f0;
            --text-muted: #64748b;
            --border:     rgba(255,255,255,0.07);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            margin: 0;
            display: flex;
        }

        /* ── Sidebar ── */
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
        .brand-sub { font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }

        .sidebar-nav { padding: 16px 12px; flex: 1; }
        .nav-label { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; padding: 8px 8px 4px; margin-top: 8px; }

        .nav-item {
            display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 8px;
            color: var(--text-muted); text-decoration: none; font-size: 0.875rem; font-weight: 500; transition: all .2s; margin-bottom: 2px;
        }

        .nav-item:hover, .nav-item.active { background: var(--navy-3); color: var(--text); }
        .nav-item.active { color: var(--accent-2); }
        .nav-item i { font-size: 1rem; width: 18px; }

        .sidebar-footer { padding: 16px; border-top: 1px solid var(--border); }
        .user-card { display: flex; align-items: center; gap: 10px; padding: 10px; border-radius: 8px; background: var(--navy-3); }
        .avatar { width: 34px; height: 34px; border-radius: 8px; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: white; }
        .user-name { font-size: 0.8rem; font-weight: 600; color: white; }
        .user-role { font-size: 0.68rem; color: var(--text-muted); }

        /* ── Main Layout ── */
        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { padding: 18px 28px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--navy-2); position: sticky; top: 0; z-index: 50; }
        .content { padding: 28px; }

        .panel { background: var(--navy-2); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
        .panel-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 10px 16px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        .data-table td { padding: 12px 16px; font-size: 0.825rem; border-bottom: 1px solid var(--border); }
        
        .badge-status { font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; font-weight: 600; }
        .badge-status.success { background: rgba(34,197,94,0.15); color: #4ade80; }
        .badge-status.warning { background: rgba(245,158,11,0.15); color: #fbbf24; }

        /* Modal Custom Styling */
        #modalTambahTeknisi input::placeholder { color: rgba(255, 255, 255, 0.4) !important;}
        .modal-content { background: var(--navy-2); border: 1px solid var(--border); border-radius: 16px; color: white; }
        .form-control, .form-select { border-color: rgba(255, 255, 255, 0.2) !important; color: white !important; }
        .form-control:focus, .form-select:focus { background: transparent; color: white; }
        .form-label { color: #ffffff !important; margin-bottom: 8px; }

        :root {
            --navy:       #0b1120;
            --navy-2:     #111827;
            --navy-3:     #1e2d45;
            --accent:     #3b82f6;
            --text:       #e2e8f0;
            --text-muted: #64748b;
            --border:     rgba(255,255,255,0.07);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            margin: 0;
            display: flex;
        }
        /* Main Content */
        .main { margin-left: 240px; flex: 1; padding: 40px; }
        .settings-card {
            background: var(--navy-2); border: 1px solid var(--border);
            border-radius: 16px; padding: 32px; max-width: 800px;
        }
        .form-label { color: #94a3b8; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; }
        .form-control {
            background: var(--navy-3); border: 1px solid rgba(255,255,255,0.1);
            color: white; padding: 12px;
        }
        .form-control:focus {
            background: var(--navy-3); border-color: var(--accent); color: white; box-shadow: none;
        }
        .section-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 24px; color: white; }
        .btn-save { background: var(--accent); border: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; }


        .settings-card {
        background: var(--navy-2); 
        border: 1px solid var(--border);
        border-radius: 16px; 
        padding: 40px; 
        width: 100%; /* Mengisi seluruh lebar .main */
        max-width: 1000px; /* Batas maksimal agar tidak terlalu melar di layar ultra-wide */
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .form-label { 
        color: var(--accent-2); 
        font-size: 0.85rem; 
        font-weight: 700; 
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control {
        background: var(--navy-3) !important; 
        border: 1px solid var(--border) !important;
        color: white !important; 
        padding: 14px;
        border-radius: 10px;
    }

    /* Placeholder agar terlihat */
    .form-control::placeholder {
        color: rgba(255,255,255,0.3) !important;
    }

    .form-control:focus {
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
    }

    .form-control:disabled {
        background: rgba(255,255,255,0.05) !important;
        color: var(--text-muted) !important;
        cursor: not-allowed;
    }

    .section-title { 
        font-size: 1.15rem; 
        font-weight: 800; 
        margin-bottom: 25px; 
        color: white; 
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .btn-save { 
        background: var(--accent); 
        border: none; 
        padding: 14px 40px; 
        border-radius: 12px; 
        font-weight: 700;
        transition: all 0.3s;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        background: #2563eb;
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
                <div class="user-name">Administrator</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>


<main class="main">
    <div class="mb-4">
        <h4 class="fw-bold">Pengaturan</h4>
        <p class="text-muted">Kelola akun administrator dan preferensi sistem Anda.</p>
    </div>

    <div class="settings-card">
        <form action="../../../app/fetch_data.php?action=update_profile" method="POST">
            <div class="section-title"><i class="bi bi-person-circle me-2"></i>Profil Administrator</div>
            <div class="row g-4 mb-5">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($namaAdmin) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-control" value="admin_nusa" disabled>
                    <small class="text-muted">Username tidak dapat diubah.</small>
                </div>
            </div>

            <div class="section-title"><i class="bi bi-shield-lock me-2"></i>Keamanan</div>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password_baru" class="form-control" placeholder="Isi jika ingin diubah">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru">
                </div>
            </div>

            <div class="mt-5 border-top border-secondary border-opacity-10 pt-4 d-flex justify-content-end">
                <button type="submit" class="btn btn-save text-white">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</main>

</body>
</html>