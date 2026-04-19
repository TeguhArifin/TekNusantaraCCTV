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
    <title>Tiket Kerja — TechMonitor</title>
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

        .bg-navy-2 { background-color: var(--navy-2) !important; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--navy);
            color: var(--text);
            margin: 0;
            display: flex;
        }

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
            color: white;
        }

        .user-name { font-size: 0.8rem; font-weight: 600; color: white; }
        .user-role { font-size: 0.68rem; color: var(--text-muted); }

        .main { margin-left: 240px; flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        
        .topbar {
            padding: 18px 28px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--navy-2);
            position: sticky; top: 0; z-index: 50;
        }

        .content { padding: 28px; }

        .panel {
            background: var(--navy-2);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }

        .panel-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            padding: 10px 16px; font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: 1px; color: var(--text-muted); border-bottom: 1px solid var(--border);
        }
        .data-table td { padding: 12px 16px; font-size: 0.825rem; border-bottom: 1px solid var(--border); }
        .ticket-id { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: var(--accent-2); }
        
        .badge-status {
            font-size: 0.7rem; padding: 3px 10px; border-radius: 20px; font-weight: 600;
        }
        .badge-status.success { background: rgba(34,197,94,0.15); color: #4ade80; }
        .badge-status.warning { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-status.danger  { background: rgba(239,68,68,0.15); color: #f87171; }
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
        <a href="admin_tiket.php" class="nav-item active">
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
                <div class="user-name">Administrator</div>
                <div class="user-role">Administrator</div>
            </div>
        </div>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div style="font-size: 1rem; font-weight: 700;">Manajemen Tiket Kerja</div>
        <button class="btn btn-primary btn-sm" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalTambahTiket">
            + Buat Tiket
        </button>
    </div>

    <div class="content">
        <div class="panel">
            <div class="panel-header">
                <span style="font-size: 0.875rem; font-weight: 700;">Daftar Tiket Kerja</span>
            </div>
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
                <tbody id="table-tiket-kerja">
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="modalTambahTiket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--navy-2); border: 1px solid var(--border); border-radius: 16px;">
            <div class="modal-header border-bottom border-secondary border-opacity-10">
                <h5 class="modal-title fw-bold" style="font-size: 1rem; color: white;">Buat Tiket Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body p-4">
    <form action="../../../app/fetch_data.php" method="POST">
        <div class="mb-4">
    <label class="form-label small fw-bold text-white">Nama Teknisi</label>
    <select name="teknisi_id" id="select-teknisi" class="form-select bg-transparent text-white border-secondary border-opacity-50" style="font-size: 0.875rem;" required>
        <option value="" class="bg-dark text-white">Memuat...</option>
    </select>
</div>
        
      <div class="mb-4">
    <label class="form-label small fw-bold text-white">Pilih Lokasi Pelanggan</label>
    <select name="pelanggan_id" id="select-lokasi" class="form-select bg-transparent text-white border-secondary border-opacity-50" style="font-size: 0.875rem;" required>
        <option value="" class="bg-dark text-white">Memuat lokasi...</option>
    </select>
</div>

        <div class="mb-4">
            <label class="form-label small fw-bold text-white">Prioritas</label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="priority" id="p1" value="rendah" checked>
                    <label class="form-check-label small text-white" for="p1">Rendah</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="priority" id="p2" value="sedang">
                    <label class="form-check-label small text-white" for="p2">Sedang</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="priority" id="p3" value="tinggi">
                    <label class="form-check-label small text-white" for="p3">Tinggi</label>
                </div>
            </div>
        </div>

        <div class="mt-4 d-grid">
            <button type="submit" class="btn btn-primary py-2" style="border-radius: 8px; font-weight: 700;">Simpan Tiket</button>
        </div>
    </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditTiket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-navy-2 border-secondary border-opacity-25" style="border-radius: 15px;"> 
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-white">Update Status Tiket</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="../../../app/fetch_data.php?action=update_tiket" method="POST">
                    <input type="hidden" name="id" id="edit-id">
                    
                    <div class="mb-4">
    <label class="form-label small fw-bold text-white">Nama Teknisi</label>
    <select name="teknisi_id" id="edit-teknisi" class="form-select bg-transparent text-white border-secondary border-opacity-50" style="font-size: 0.875rem;" required>
    </select>
</div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-white">Ubah Status</label>
                        <select name="status" id="edit-status" class="form-select bg-transparent text-white border-secondary border-opacity-50" style="font-size: 0.875rem;">
                            <option value="pending" class="bg-dark">Pending</option>
                            <option value="proses" class="bg-dark">Proses</option>
                            <option value="selesai" class="bg-dark">Selesai</option>
                            <option value="batal" class="bg-dark">Batal</option>
                        </select>
                    </div>

                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-bold" style="border-radius: 8px;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailLaporan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-navy-2 border-secondary border-opacity-25" style="border-radius: 16px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-white">Laporan Hasil Kerja</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="isi-laporan">
                </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// 1. Fungsi Refresh Tabel Tiket secara Real-time
function refreshTiket() {
    $.getJSON('../../../app/fetch_data.php?action=get_aktivitas', function(data) {
        let rows = '';
        const badges = { 'selesai': 'success', 'proses': 'warning', 'pending': 'danger', 'batal': 'secondary' };
        
        data.forEach(item => {
            // 1. Format Nomor WA Teknisi (Penerima)
            let phoneTeknisi = item.wa_teknisi ? item.wa_teknisi.replace(/\D/g, '') : '';
            if (phoneTeknisi.startsWith('0')) phoneTeknisi = '62' + phoneTeknisi.substr(1);

            // 2. Format Nomor WA Pelanggan (Untuk di dalam pesan)
            let phonePelanggan = item.wa_pelanggan || '-';

            // 3. Susun Pesan (Menambahkan Nomor Pelanggan)
            const pesan = encodeURIComponent(
                `Halo ${item.nama_teknisi},\n\n` +
                `Ada tugas baru *${item.id_tiket}*.\n\n` +
                `*Detail Lokasi:* \n` +
                `Pelanggan: ${item.lokasi}\n` +
                `Alamat: ${item.alamat}\n` +
                `WA Pelanggan: ${phonePelanggan}\n\n` + // <-- Tambahan di sini
                `Mohon segera dikerjakan. Terima kasih.`
            );
            
            const waLink = phoneTeknisi ? `https://wa.me/${phoneTeknisi}?text=${pesan}` : '#';
            const waColor = phoneTeknisi ? 'text-success' : 'text-muted';

            // 4. Render Baris Tabel
            rows += `
            <tr>
                <td><span class="ticket-id">${item.id_tiket}</span></td>
                <td>${item.nama_teknisi}</td>
                <td>${item.lokasi}</td>
                <td><span class="badge-status ${badges[item.status.toLowerCase()]}">${item.status.toUpperCase()}</span></td>
                <td style="color:var(--text-muted); font-family:'JetBrains Mono';">${item.jam} WIB</td>
                <td class="text-end">
                    <a href="${waLink}" target="_blank" class="btn btn-link ${waColor} p-0 me-2" title="Kirim WA ke Teknisi">
                        <i class="bi bi-whatsapp"></i>
                    </a>
                    <button class="btn btn-link text-primary p-0" data-bs-toggle="modal" data-bs-target="#modalEditTiket" 
                            onclick="fillEditModal('${item.id}', '${item.teknisi_id}', '${item.status}')">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#table-tiket-kerja').html(rows);
    });
}

function lihatLaporan(id) {
    $('#modalDetailLaporan').modal('show');
    $.getJSON('../../../app/fetch_data.php?action=get_laporan_admin&id=' + id, function(res) {
        if(res.status === 'success') {
            $('#isi-laporan').html(`
                <div class="text-center mb-3">
                    <img src="../../../Storage/${res.data.foto}" class="img-fluid rounded border" style="max-height:300px">
                </div>
                <p><strong>Teknisi:</strong> ${res.data.teknisi}</p>
                <p><strong>Tanggal Selesai:</strong> ${res.data.tgl}</p>
                <p><strong>Catatan:</strong><br><span class="text-muted">${res.data.catatan}</span></p>
            `);
        } else {
            $('#isi-laporan').html('<p class="text-center text-danger">Laporan belum diupload oleh teknisi.</p>');
        }
    });
}

// 2. Fungsi Mengisi Data ke Modal Edit
function fillEditModal(id, teknisiId, status) {
    // 1. Set ID Tiket dan Status
    $('#edit-id').val(id);
    $('#edit-status').val(status.toLowerCase());

    // 2. Kosongkan dulu dropdown agar tidak menumpuk
    $('#edit-teknisi').empty().append('<option value="" class="bg-dark">Memuat...</option>');

    // 3. Ambil data teknisi dari server
    $.getJSON('../../../app/fetch_data.php?action=get_teknisi_all', function(data) {
        let options = '';
        data.forEach(t => {
            // Bandingkan ID teknisi dari database dengan yang sedang bertugas
            let selected = (t.id == teknisiId) ? 'selected' : '';
            options += `<option value="${t.id}" class="bg-dark text-white" ${selected}>${t.nama}</option>`;
        });
        
        // 4. Masukkan ke elemen dengan ID edit-teknisi
        $('#edit-teknisi').html(options); 
    }).fail(function() {
        alert("Gagal mengambil data teknisi. Cek koneksi atau file fetch_data.php");
    });
}

function loadLokasiToSelect() {
    $.getJSON('../../../app/fetch_data.php?action=get_lokasi_all', function(data) {
        let options = '<option value="" class="bg-dark text-white">Pilih Pelanggan / Lokasi</option>';
        data.forEach(l => {
            options += `<option value="${l.id}" class="bg-dark text-white">${l.nama} - ${l.alamat}</option>`;
        });
        $('#select-lokasi').html(options);
    }).fail(function() {
        $('#select-lokasi').html('<option value="" class="bg-dark text-danger">Gagal memuat lokasi</option>');
    });
}




// 3. Fungsi Load Teknisi untuk Modal Tambah Tiket
function loadTeknisiToSelect() {
    $.getJSON('../../../app/fetch_data.php?action=get_teknisi_all', function(data) {
        // Tampilkan pesan default jika data kosong
        let options = '<option value="" class="bg-dark text-white">Pilih Teknisi</option>';
        
        data.forEach(t => {
            // Hanya tampilkan teknisi dengan status aktif
            if(t.status.toLowerCase() === 'aktif') {
                options += `<option value="${t.id}" class="bg-dark text-white">${t.nama}</option>`;
            }
        });
        
        // Masukkan data ke elemen ID select-teknisi
        $('#select-teknisi').html(options);
    }).fail(function() {
        console.error("Gagal mengambil data teknisi.");
        $('#select-teknisi').html('<option value="" class="bg-dark text-danger">Gagal memuat data</option>');
    });
}

// Panggil fungsi ini di dalam $(document).ready
$(document).ready(function() {
    // Jalankan semua fungsi load data saat halaman siap
    refreshTiket();
    loadTeknisiToSelect();
    loadLokasiToSelect();
    
    // Set interval hanya untuk refresh tabel tiket
    setInterval(refreshTiket, 5000); 
});
</script>

</body>
</html>