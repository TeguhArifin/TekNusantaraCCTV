<?php
session_start();

// Guard: hanya teknisi yang boleh akses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: ../auth/login.php');
    exit;
}

$namaTeknisi = $_SESSION['nama_lengkap'] ?? 'Teknisi';
$words = explode(' ', $namaTeknisi);
$inisial = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMonitor Mobile — Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0b1120; 
            --navy-2: #111827; 
            --navy-3: #1e2d45;
            --accent: #3b82f6; 
            --accent-2: #60a5fa;
            --text: #f8fafc; /* Warna teks utama lebih terang */
            --text-white: #94a3b8; /* Warna teks redup lebih terlihat */
            --border: rgba(255,255,255,0.1);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--navy); 
            color: var(--text); 
            margin: 0;
        }

        /* --- Sidebar Logic --- */
        .sidebar { 
            width: 260px; 
            min-height: 100vh; 
            background: var(--navy-2); 
            border-right: 1px solid var(--border); 
            position: fixed; 
            top: 0; left: 0; bottom: 0; 
            z-index: 1050;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand { padding: 25px 20px; border-bottom: 1px solid var(--border); }
        .brand-name { font-size: 1.25rem; font-weight: 800; color: var(--accent-2); }
        
        .nav-label { 
            font-size: 0.7rem; 
            color: var(--accent-2); 
            text-transform: uppercase; 
            letter-spacing: 1.5px; 
            padding: 20px 20px 10px; 
            font-weight: 700;
        }

        .nav-item {
            display: flex; align-items: center; gap: 12px; padding: 12px 20px;
            color: var(--text-white); text-decoration: none; font-weight: 500;
            transition: 0.2s;
        }

        .nav-item:hover, .nav-item.active { color: var(--text); background: var(--navy-3); }
        .nav-item.active { color: var(--accent-2); border-right: 3px solid var(--accent-2); }
        .nav-item i { font-size: 1.1rem; }

        .main { 
            margin-left: 260px; 
            padding: 30px; 
            transition: 0.3s; 
        }

        /* Tombol menu toggle - Sembunyi di Desktop */
        .menu-toggle { display: none; cursor: pointer; color: var(--text); }

        /* --- Responsive Mode (Mobile/S24 FE) --- */
        @media (max-width: 576px) {
    .modal-dialog {
        display: flex;
        align-items: center;
        min-height: calc(100% - 1rem);
    }
}

        /* Card Styles */
        .greeting-card { background: linear-gradient(135deg, var(--navy-2) 0%, var(--navy-3) 100%); border: 1px solid var(--border); border-radius: 18px; padding: 20px; margin-bottom: 20px; }
        .stat-box { background: var(--navy-2); padding: 15px; border-radius: 15px; border: 1px solid var(--border); text-align: center; }
        .task-card { background: var(--navy-2); border-radius: 16px; padding: 20px; margin-bottom: 15px; border: 1px solid var(--border); border-left: 5px solid var(--accent); }
    </style>
</head>
<body>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-name">TechMonitor</div>
        <div class="small text-white">Field Technician</div>
    </div>
    <nav class="flex-grow-1">
        <div class="nav-label">Menu Utama</div>
        <a href="teknisi_dashboard.php" class="nav-item active">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="teknisi_tugas.php" class="nav-item">
            <i class="bi bi-ticket-detailed-fill"></i> Tugas Saya
        </a>
        <div class="nav-label">Pengaturan</div>
        <a href="teknisi_pengaturan.php" class="nav-item">
            <i class="bi bi-person-gear"></i> Profil & Akun
        </a>
    </nav>
    <div class="p-3">
        <form action="../../../app/controllers/AuthController.php" method="POST">
            <button type="submit" name="logout" class="btn btn-outline-danger w-100 fw-bold border-0 py-2">
                <i class="bi bi-box-arrow-right me-2"></i> Log Out
            </button>
        </form>
    </div>
</aside>

<main class="main">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="menu-toggle" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </div>
            <div>
                <h5 class="fw-800 mb-0 text-primary">TechMonitor </h5>
                <small id="displayDate" class="text-white"></small>
            </div>
        </div>
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow" style="width:45px; height:45px;">
            <?= $inisial ?>
        </div>
    </div>

    <div class="greeting-card">
        <h6 class="fw-bold mb-1">Halo, <?= explode(' ', $namaTeknisi)[0] ?>!</h6>
        <p class="small text-white mb-0">Kamu memiliki tugas baru yang perlu diproses.</p>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-4"><div class="stat-box"><div class="text-primary fw-bold" id="stat-pending">0</div><div class="stat-label">Pending</div></div></div>
        <div class="col-4"><div class="stat-box"><div class="text-warning fw-bold" id="stat-proses">0</div><div class="stat-label">Proses</div></div></div>
        <div class="col-4"><div class="stat-box"><div class="text-success fw-bold" id="stat-selesai">0</div><div class="stat-label">Done</div></div></div>
    </div>

    <h6 class="fw-bold mb-3">Daftar Tugas Aktif</h6>
    <div id="task-list">
        <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
    </div>
</main>

<div class="modal fade" id="modalLaporan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-secondary shadow-lg" style="border-radius: 20px; margin: 0 15px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-white">Laporan Pekerjaan</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formLaporan">
                <div class="modal-body">
                    <input type="hidden" name="id" id="report-id">
                    <div class="mb-3">
                        <label class="form-label small text-white">Ambil Foto Bukti</label>
                        <input type="file" name="foto" class="form-control bg-transparent text-white border-secondary" accept="image/*" capture="camera" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-white">Catatan Kendala</label>
                        <textarea name="catatan" class="form-control bg-transparent text-white border-secondary" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold">Selesaikan Tugas</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }

    const d = new Date();
    document.getElementById('displayDate').textContent = d.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'short' });

    function loadData() {
        $.getJSON('../../../app/fetch_data.php?action=get_tugas_teknisi', function(data) {
            $('#stat-pending').text(data.stats.pending);
            $('#stat-proses').text(data.stats.proses);
            $('#stat-selesai').text(data.stats.selesai);
            
            let html = '';
            if (data.list.length === 0) {
                html = '<div class="text-center text-white py-5">Tidak ada tugas aktif</div>';
            } else {
                data.list.forEach(t => {
                    const isPending = t.status === 'Pending';
                    html += `
                    <div class="task-card ${isPending ? '' : 'proses'}">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-primary fw-bold">#TK-${t.id}</span>
                            <span class="badge ${isPending ? 'bg-danger' : 'bg-warning'} text-dark">${t.status}</span>
                        </div>
                        <div class="fw-bold text-white mb-1">${t.lokasi}</div>
                        <div class="small text-white mb-3"><i class="bi bi-geo-alt me-1"></i>${t.alamat}</div>
                        <button onclick="handleStatus(${t.id}, '${t.status}')" class="btn ${isPending ? 'btn-primary' : 'btn-success'} w-100 py-2 fw-bold shadow-sm">
                            ${isPending ? 'Mulai Kerja' : 'Selesaikan & Lapor'}
                        </button>
                    </div>`;
                });
            }
            $('#task-list').html(html);
        });
    }

    function handleStatus(id, current) {
        if (current === 'Pending') {
            $.post('../../../app/fetch_data.php?action=update_status_teknisi', { id: id, status: 'proses' }, function() {
                loadData();
            });
        } else {
            $('#report-id').val(id);
            $('#modalLaporan').modal('show');
        }
    }

    $('#formLaporan').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $.ajax({
            url: '../../../app/fetch_data.php?action=submit_laporan_teknisi',
            type: 'POST',
            data: formData,
            processData: false, contentType: false,
            success: function() {
                $('#modalLaporan').modal('hide');
                loadData();
                alert('Tugas Selesai!');
            }
        });
    });

    $(document).ready(function() {
        loadData();
        setInterval(loadData, 5000);
    });
</script>
</body>
</html>