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
    <title>Tugas Saya — TechMonitor</title>
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
        .main { margin-left: 260px; padding: 30px; transition: 0.3s; }

        /* --- Mobile Responsiveness --- */
        .menu-toggle { display: none; cursor: pointer; color: var(--text-bright); }
        @media (max-width: 992px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; box-shadow: 15px 0 40px rgba(0,0,0,0.7); }
            .main { margin-left: 0; padding: 20px; }
            .menu-toggle { display: block; font-size: 1.8rem; }
        }

        /* --- History Cards --- */
        .history-card { 
            background: var(--navy-2); border-radius: 18px; padding: 20px; margin-bottom: 15px; border: 1px solid var(--border);
            transition: 0.3s;
        }
        .history-card:hover { border-color: var(--accent); transform: translateY(-2px); }
        .badge-status { font-size: 0.7rem; padding: 5px 12px; border-radius: 10px; font-weight: 700; text-transform: uppercase; }
        
        .empty-state { padding: 80px 20px; text-align: center; color: var(--text-gray); }
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
        <a href="teknisi_dashboard.php" class="nav-item">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="teknisi_tugas.php" class="nav-item active">
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
            <div class="menu-toggle" onclick="toggleSidebar()"><i class="bi bi-list"></i></div>
            <h5 class="fw-800 mb-0">Riwayat <span class="text-primary">Tugas</span></h5>
        </div>
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fw-bold shadow" style="width:45px; height:45px;">
            <?= $inisial ?>
        </div>
    </div>

    <div class="mb-4">
        <div class="input-group">
            <span class="input-group-text bg-navy-2 border-border text-gray"><i class="bi bi-search"></i></span>
            <input type="text" id="searchTugas" class="form-control bg-navy-2 border-border text-white" placeholder="Cari lokasi atau ID tiket...">
        </div>
    </div>

    <div id="history-list">
        <div class="text-center py-5"><div class="spinner-border text-primary"></div></div>
    </div>
</main>

<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mx-3">
        <div class="modal-content" style="background: var(--navy-2); border: 1px solid var(--border); border-radius: 20px;">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-white w-100 text-center">DETAIL PEKERJAAN</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4">
                <div id="detail-content">
                    <div class="text-center py-4"><div class="spinner-border text-primary"></div></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }

    function loadHistory() {
        $.getJSON('../../../app/fetch_data.php?action=get_riwayat_teknisi', function(data) {
            let html = '';
            if (data.length === 0) {
                html = '<div class="empty-state">Belum ada riwayat tugas.</div>';
            } else {
                data.forEach(t => {
                    const statusClass = t.status === 'Selesai' ? 'bg-success text-white' : 'bg-warning text-dark';
                    html += `
                    <div class="history-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-primary fw-bold">#TK-${t.id}</span>
                                <div class="fw-bold text-white mt-1 fs-6">${t.lokasi}</div>
                            </div>
                            <span class="badge-status ${statusClass}">${t.status}</span>
                        </div>
                        <div class="small text-white mb-3"><i class="bi bi-geo-alt me-2"></i>${t.alamat}</div>
                        <div class="d-flex justify-content-between align-items-center pt-3 border-top border-border">
                            <div class="small text-white" style="font-size: 0.75rem;"><i class="bi bi-calendar-check me-1"></i>${t.waktu}</div>
                            <button onclick="showDetail(${t.id})" class="btn btn-link btn-sm text-accent-2 p-0 text-decoration-none fw-bold">Detail Laporan</button>
                        </div>
                    </div>`;
                });
            }
            $('#history-list').html(html);
        });
    }

    function showDetail(id) {
    $('#modalDetail').modal('show');
    $('#detail-content').html('<div class="text-center py-4"><div class="spinner-border text-primary\"></div></div>');
    
    $.getJSON('../../../app/fetch_data.php?action=get_detail_tugas&id=' + id, function(res) {
        let detailHtml = `
            <div class="text-center mb-4">
                <img src="../../../Storage/${res.foto}" 
                     class="img-fluid rounded-3 mb-3 border border-secondary" 
                     style="max-height: 250px; width: 100%; object-fit: cover;"
                     onerror="this.src='https://placehold.co/400x300?text=Gambar+Tidak+Ada'">
            </div>
            <div class="mb-3">
                <label class="small text-white d-block">Tanggal Selesai</label>
                <div class="fw-bold text-white">${res.tgl}</div>
            </div>
            <div class="mb-0">
                <label class="small text-white d-block">Keterangan Pekerjaan</label>
                <div class="p-3 rounded-3 bg-dark border border-secondary text-white-50" style="font-size: 0.9rem;">
                    ${res.catatan}
                </div>
            </div>
        `;
        $('#detail-content').html(detailHtml);
    });
}

    // Fitur Pencarian Sederhana
    $("#searchTugas").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".history-card").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    $(document).ready(function() { loadHistory(); });
</script>
</body>
</html>