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
    <title>Data Teknisi — TechMonitor</title>
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
        <a href="admin_teknisi.php" class="nav-item active">
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
                <div class="user-name">Administrator</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div style="font-weight: 700;">Data Teknisi Lapangan</div>
        <button class="btn btn-primary btn-sm px-3" style="border-radius: 8px;" data-bs-toggle="modal" 
        data-bs-target="#modalTambahTeknisi">+ Tambah Teknisi</button>
    </div>

    <div class="content">
        <div class="panel">
            <div class="panel-header">
                <span style="font-size: 0.875rem; font-weight: 700;">Daftar Teknisi</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Teknisi</th>
                        <th>Spesialis</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-data-teknisi">
                    </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="modalEditTeknisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary border-opacity-10">
                <h5 class="modal-title fw-bold" style="font-size: 1rem;">Edit Data Teknisi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="../../../app/fetch_data.php" method="POST">
                    <input type="hidden" name="id_teknisi" id="edit_id">
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nama Teknisi</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control bg-transparent" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Spesialisasi</label>
                        <input type="text" name="spesialis" id="edit_spesialis" class="form-control bg-transparent" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nomor WhatsApp</label>
                        <input type="number" name="no_hp" id="edit_no_hp" class="form-control bg-transparent" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold">Status</label>
                        <select name="status" id="edit_status" class="form-select bg-transparent">
                            <option value="aktif" class="bg-dark">Aktif</option>
                            <option value="nonaktif" class="bg-dark">Nonaktif</option>
                        </select>
                    </div>
                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-2" style="border-radius: 8px; font-weight: 700;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHapusTeknisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body p-4">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="fw-bold">Hapus Teknisi?</h5>
                <p class="text-white small">Anda yakin ingin menghapus <span id="namaTeknisiHapus" class="text-white fw-bold"></span>?</p>
                <div class="d-flex gap-2 mt-4">
                    <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                    <a id="linkHapusTeknisi" href="#" class="btn btn-danger w-100" style="border-radius: 8px; font-weight: 600;">Hapus</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahTeknisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-bottom border-secondary border-opacity-10">
                <h5 class="modal-title fw-bold" style="font-size: 1rem; color: white;">Tambah Teknisi Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="../../../app/fetch_data.php" method="POST">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-white">Nama Lengkap Teknisi</label>
                        <input type="text" name="nama" class="form-control bg-transparent text-white border-secondary border-opacity-50" 
                               placeholder="Masukkan nama lengkap" style="font-size: 0.875rem;" required>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-white">Spesialisasi</label>
                        <input type="text" name="spesialis" class="form-control bg-transparent text-white border-secondary border-opacity-50" 
                               placeholder="Contoh: CCTV IP, Networking, Access Control" style="font-size: 0.875rem;" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Nomor WhatsApp</label>
                        <input type="number" name="no_hp" class="form-control bg-transparent" required>
                        <div class="form-text text-muted" style="font-size: 0.7rem;">Gunakan format 628...</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-white">Status Awal</label>
                        <select name="status" class="form-select bg-transparent text-white border-secondary border-opacity-50" style="font-size: 0.875rem;">
                            <option value="aktif" class="bg-dark text-white">Aktif (Siap Tugas)</option>
                            <option value="nonaktif" class="bg-dark text-white">Nonaktif (Nonaktif)</option>
                        </select>
                    </div>

                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-2" style="border-radius: 8px; font-weight: 700;">
                            Simpan Data Teknisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
function fillEditModal(id, nama, spesialis, status, no_hp) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_spesialis').value = spesialis;
    document.getElementById('edit_status').value = status;
    document.getElementById('edit_no_hp').value = no_hp; 
}

function prepareDelete(id, nama) {
    document.getElementById('namaTeknisiHapus').innerText = nama;
    document.getElementById('linkHapusTeknisi').setAttribute('href', '../../../app/fetch_data.php?action=delete_teknisi&id=' + id);
}

function refreshTeknisi() {
    $.getJSON('../../../app/fetch_data.php?action=get_teknisi_all', function(data) {
        let rows = '';
        data.forEach(t => {
            // Mapping status untuk badge CSS
            let badgeClass = (t.status.toLowerCase() === 'aktif') ? 'success' : 'warning';
            let displayStatus = (t.status.toLowerCase() === 'aktif') ? 'Aktif' : 'Izin';

            rows += `
            <tr>
                <td style="font-family:'JetBrains Mono'; color:var(--accent-2);">${t.id_format}</td>
                <td class="fw-bold">${t.nama}</td>
                <td>${t.spesialisasi || t.spesialis}</td>
                <td><span class="badge-status ${badgeClass}">${displayStatus}</span></td>
                <td class="text-center">
                    <i class="bi bi-pencil-square me-2 text-primary" style="cursor:pointer" 
                       data-bs-toggle="modal" 
                       data-bs-target="#modalEditTeknisi"
                       onclick="fillEditModal('${t.id}', '${t.nama}', '${t.spesialisasi}', '${t.status}', '${t.no_hp}')"></i>
                       <i class="bi bi-trash text-danger" style="cursor:pointer"
                       data-bs-toggle="modal" 
                       data-bs-target="#modalHapusTeknisi"
                       onclick="prepareDelete('${t.id}', '${t.nama}')"></i>
                </td>
            </tr>`;
        });
        $('#table-data-teknisi').html(rows);
    });
}

$(document).ready(function() {
    refreshTeknisi();
    setInterval(refreshTeknisi, 5000);

    // Menangani Notifikasi Berdasarkan Parameter URL
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');

    if (status === 'success') {
        alert('Data Teknisi Berhasil Ditambahkan!');
    } else if (status === 'success_update') {
        alert('Data Teknisi Berhasil Diubah!');
    } else if (status === 'deleted' || status === 'success_delete') {
        alert('Data Teknisi Berhasil Dihapus!');
    }

    // Opsional: Bersihkan parameter di URL setelah alert muncul agar tidak muncul lagi saat refresh
    if (status) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
</body>
</html>