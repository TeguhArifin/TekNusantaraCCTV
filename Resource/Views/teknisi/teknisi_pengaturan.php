<?php
session_start();

// Guard: hanya teknisi yang boleh akses
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'teknisi') {
    header('Location: ../auth/login.php');
    exit;
}

$id_user = $_SESSION['user_id'];
$namaTeknisi = $_SESSION['nama_lengkap'] ?? 'Teknisi';
$words = explode(' ', $namaTeknisi);
$inisial = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil & Akun — TechMonitor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #0b1120; --navy-2: #111827; --navy-3: #1e2d45;
            --accent: #3b82f6; --accent-2: #60a5fa;
            --text-bright: #ffffff; --text-gray: #cbd5e1;
            --border: rgba(255,255,255,0.15);
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--navy); color: var(--text-bright); margin: 0; }

        /* --- Sidebar --- */
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

        @media (max-width: 992px) {
            .sidebar { left: -260px; }
            .sidebar.active { left: 0; }
            .main { margin-left: 0; padding: 20px; }
        }

        /* --- Profile Card --- */
        .profile-header {
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            border-radius: 20px; padding: 30px; text-align: center; margin-bottom: 25px;
        }
        .avatar-large {
            width: 80px; height: 80px; background: rgba(255,255,255,0.2);
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 2rem; font-weight: 800; margin: 0 auto 15px; border: 3px solid rgba(255,255,255,0.3);
        }
        .card-settings {
            background: var(--navy-2); border-radius: 20px; border: 1px solid var(--border); padding: 25px;
        }
        .form-label { color: var(--accent-2); font-weight: 700; font-size: 0.8rem; text-transform: uppercase; }
        .form-control { 
    background: var(--navy-3); 
    border: 1px solid var(--border); 
    color: white; 
    border-radius: 12px; 
    padding: 12px;
}
        .form-control:focus { background: var(--navy-3); border-color: var(--accent); color: white; box-shadow: none; }
        .form-control::placeholder {
    color: rgba(255, 255, 255, 0.6) !important;
}

/* Jika ingin putih solid saat fokus */
.form-control:focus::placeholder {
    color: rgba(255, 255, 255, 0.8);
}
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
        <a href="teknisi_tugas.php" class="nav-item">
            <i class="bi bi-ticket-detailed-fill"></i> Tugas Saya
        </a>
        <div class="nav-label">Pengaturan</div>
        <a href="teknisi_pengaturan.php" class="nav-item active">
            <i class="bi bi-person-gear"></i> Profil & Akun
        </a>
    </nav>
    <div class="p-3">
        <form action="../../../app/controllers/AuthController.php" method="POST">
            <button type="submit" name="logout" class="btn btn-outline-danger w-100 fw-bold border-0">
                <i class="bi bi-box-arrow-right me-2"></i> Log Out
            </button>
        </form>
    </div>
</aside>

<main class="main">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="menu-toggle d-lg-none" onclick="toggleSidebar()"><i class="bi bi-list fs-2"></i></div>
        <h5 class="fw-800 mb-0">Profil <span class="text-primary">& Akun</span></h5>
    </div>

    <div class="profile-header">
        <div class="avatar-large"><?= $inisial ?></div>
        <h4 class="fw-800 mb-1"><?= $namaTeknisi ?></h4>
        <div class="badge bg-white text-primary px-3 py-2 rounded-pill fw-bold">Field Technician</div>
    </div>

    <div class="card-settings">
        <form id="formUpdateProfil">
            <div class="mb-4">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?= $namaTeknisi ?>" required>
            </div>
            
            <hr class="my-4" >
            
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="pw_baru" class="form-control" placeholder="Isi jika ingin ganti password">
            </div>
            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="pw_konfirmasi" class="form-control" placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-3 fw-800 rounded-3 shadow">
                Simpan Perubahan
            </button>
        </form>
    </div>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    function toggleSidebar() { document.getElementById('sidebar').classList.toggle('active'); }

    $('#formUpdateProfil').on('submit', function(e) {
        e.preventDefault();
        const data = $(this).serialize();
        
        $.post('../../../app/fetch_data.php?action=update_profil_teknisi', data, function(res) {
            const response = JSON.parse(res);
            alert(response.message);
            if(response.status === 'success') location.reload();
        });
    });
</script>
</body>
</html>