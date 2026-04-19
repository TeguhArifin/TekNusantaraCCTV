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
    <title>Manajemen Lokasi — TechMonitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:       #0b1120;
            --navy-2:     #111827;
            --navy-3:     #1e2d45;
            --accent:     #3b82f6;
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

        /* ── Main Layout ── */
        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { padding: 18px 28px; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; background: var(--navy-2); position: sticky; top: 0; z-index: 50; }
        .content { padding: 28px; }

        .panel { background: var(--navy-2) !important; border: 1px solid var(--border); border-radius: 14px; overflow: hidden; }
        .panel-header { padding: 16px 20px; border-bottom: 1px solid var(--border); }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { padding: 10px 16px; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--text-white); border-bottom: 1px solid var(--border); }
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

        /* Membuat tulisan placeholder lebih terang dan kontras */
.form-control::placeholder, 
.form-select::placeholder,
textarea::placeholder {
    color: rgba(255, 255, 255, 0.5) !important;
    opacity: 1; /* Penting agar warna muncul maksimal di Firefox */
}

/* Memberikan warna teks yang jelas saat admin sedang mengetik */
.form-control, .form-select, textarea {
    color: #ffffff !important;
    background-color: rgba(255, 255, 255, 0.05) !important;
}

/* Memberikan highlight biru saat kotak input diklik (fokus) */
.form-control:focus, .textarea:focus {
    border-color: var(--accent) !important;
    background-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.15);
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
        <a href="admin_lokasi.php" class="nav-item active">
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

<main class="main">
    <div class="topbar">
        <div style="font-weight: 700;">Daftar Lokasi Pelanggan</div>
        <button class="btn btn-primary btn-sm px-3" 
        style="border-radius: 8px;" 
        data-bs-toggle="modal" 
        data-bs-target="#modalTambahLokasi">
    + Tambah Lokasi
</button>
    </div>

    <div class="content">
        <div class="panel">
            <div class="panel-header">
                <span style="font-size: 0.875rem; font-weight: 700;">Data Lokasi CCTV</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pelanggan</th>
                        <th>Alamat Lengkap</th>
                        <th>Kontak</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-lokasi">
                    </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="modalTambahLokasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-navy-2 border-secondary border-opacity-25" style="border-radius: 16px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-white">Tambah Lokasi Pelanggan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="../../../app/fetch_data.php?action=tambah_lokasi" method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Nama Pelanggan / Perusahaan</label>
                        <input type="text" name="nama" class="form-control bg-transparent text-white border-secondary border-opacity-50" placeholder="Contoh: PT. Maju Bersama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Nomor Telepon</label>
                        <input type="text" name="no_hp" class="form-control bg-transparent text-white border-secondary border-opacity-50" placeholder="021-xxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Alamat Lengkap</label>
                        <textarea name="alamat" class="form-control bg-transparent text-white border-secondary border-opacity-50" rows="3" required></textarea>
                    </div>
                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Simpan Lokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditLokasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-navy-2 border-secondary border-opacity-25" style="border-radius: 16px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-white">Edit Lokasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="../../../app/fetch_data.php?action=update_lokasi" method="POST">
                    <input type="hidden" name="id" id="edit-id-lokasi">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Nama Pelanggan</label>
                        <input type="text" name="nama" id="edit-nama-lokasi" class="form-control bg-transparent text-white border-secondary border-opacity-50" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Nomor Telepon</label>
                        <input type="text" name="no_hp" id="edit-hp-lokasi" class="form-control bg-transparent text-white border-secondary border-opacity-50">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-white">Alamat Lengkap</label>
                        <textarea name="alamat" id="edit-alamat-lokasi" class="form-control bg-transparent text-white border-secondary border-opacity-50" rows="3" required></textarea>
                    </div>
                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold">Update Lokasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Tambahkan fungsi pendukung ini di dalam tag <script> Anda
function fillEditLokasi(id, nama, hp, alamat) {
    $('#edit-id-lokasi').val(id);
    $('#edit-nama-lokasi').val(nama);
    $('#edit-hp-lokasi').val(hp);
    $('#edit-alamat-lokasi').val(alamat);
}

function hapusLokasi(id) {
    if(confirm('Apakah Anda yakin ingin menghapus lokasi ini? Semua tiket terkait lokasi ini mungkin akan terpengaruh.')) {
        window.location.href = `../../../app/fetch_data.php?action=delete_lokasi&id=${id}`;
    }
}

function refreshLokasi() {
    $.getJSON('../../../app/fetch_data.php?action=get_lokasi_all', function(data) {
        let rows = '';
        data.forEach(l => {
            rows += `
            <tr>
                <td style="font-family:'JetBrains Mono'; color:var(--accent-2);">LOC-0${l.id}</td>
                <td class="fw-bold text-white">${l.nama}</td>
                <td class="text-white small">${l.alamat}</td>
                <td><i class="bi bi-telephone me-2"></i>${l.no_hp || '-'}</td>
                <td class="text-center">
                    <button class="btn btn-link text-primary p-0 me-2" data-bs-toggle="modal" data-bs-target="#modalEditLokasi" onclick="fillEditLokasi('${l.id}', '${l.nama}', '${l.no_hp}', '${l.alamat}')">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <button class="btn btn-link text-danger p-0" onclick="hapusLokasi('${l.id}')">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#table-lokasi').html(rows);
    });
}
$(document).ready(function() {
    refreshLokasi();
    setInterval(refreshLokasi, 5000);
});
</script>
</body>
</html>