<?php
// 1. Inisialisasi Session dan Koneksi Database
session_start();
include "config.php"; 

// 2. Tentukan Aksi Berdasarkan Parameter URL
$action = $_GET['action'] ?? '';

// --- BAGIAN GET DATA (DISPLAY REAL-TIME & DROPDOWN) ---

// Statistik Dashboard Admin
if ($action == 'get_stats') {
    $t_teknisi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM teknisi"))['total'];
    $t_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='pending'"))['total'];
    $t_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='selesai'"))['total'];
    $t_hari_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE DATE(tanggal) = CURDATE()"))['total'];
    
    echo json_encode([
        'total_teknisi' => $t_teknisi,
        'tiket_pending' => $t_pending,
        'tiket_selesai' => $t_selesai,
        'tiket_hari_ini' => $t_hari_ini
    ]);
    exit;
}

// Aktivitas Tiket (Tabel Utama)
if ($action == 'get_aktivitas') {
    $sql = "SELECT t.id, t.judul, t.status, t.updated_at, 
                   tk.id as teknisi_id, tk.nama as nama_teknisi, tk.no_hp as wa_teknisi, 
                   p.nama as lokasi, p.alamat, p.no_hp as wa_pelanggan 
            FROM tugas t 
            JOIN teknisi tk ON t.teknisi_id = tk.id 
            JOIN pelanggan p ON t.pelanggan_id = p.id
            ORDER BY t.updated_at DESC";
            
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $row['id_tiket'] = "TK-" . str_pad($row['id'], 3, "0", STR_PAD_LEFT);
        $row['jam'] = date('H:i', strtotime($row['updated_at'])); 
        $row['tgl_full'] = date('Y-m-d', strtotime($row['updated_at']));
        $data[] = $row;
    }
    echo json_encode($data);
    exit;
}

if ($action == 'get_laporan_admin') {
    $tugas_id = $_GET['id'];
    // Ambil data laporan join dengan teknisi untuk tahu siapa yang mengerjakan
    $sql = "SELECT l.*, tk.nama as nama_teknisi 
            FROM laporan l 
            JOIN teknisi tk ON l.teknisi_id = tk.id 
            WHERE l.tugas_id = '$tugas_id'";
    $query = mysqli_query($conn, $sql);
    $res = mysqli_fetch_assoc($query);

    if ($res) {
        echo json_encode([
            'status' => 'success',
            'data' => [
                'foto' => $res['foto'],
                'catatan' => $res['catatan'],
                'teknisi' => $res['nama_teknisi'],
                'tgl' => date('d M Y', strtotime($res['tanggal_selesai']))
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

if ($action == 'get_laporan_hari_ini') {
    // Mengambil data tugas yang diperbarui hari ini
    $sql = "SELECT t.updated_at as waktu, tk.nama as teknisi, p.nama as lokasi, t.status 
            FROM tugas t
            JOIN teknisi tk ON t.teknisi_id = tk.id
            JOIN pelanggan p ON t.pelanggan_id = p.id
            WHERE DATE(t.updated_at) = CURDATE()
            ORDER BY t.updated_at DESC";
    
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

if ($action == 'update_status_teknisi') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE tugas SET status='$status', updated_at=NOW() WHERE id='$id'");
    exit;
}

if ($action == 'get_riwayat_teknisi') {
    $id_user = $_SESSION['user_id'];
    
    // Cari ID teknisi berdasarkan user_id
    $q_tek = mysqli_query($conn, "SELECT id FROM teknisi WHERE user_id = '$id_user'");
    $tek = mysqli_fetch_assoc($q_tek);
    $id_teknisi = $tek['id'];

    // Ambil tugas yang statusnya Selesai
    $query = mysqli_query($conn, "SELECT t.*, p.nama as lokasi_p, p.alamat 
                                   FROM tugas t 
                                   LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
                                   WHERE t.teknisi_id = '$id_teknisi' AND t.status = 'selesai' 
                                   ORDER BY t.updated_at DESC");
    $list = [];
    while($row = mysqli_fetch_assoc($query)) {
        $list[] = [
            'id'     => $row['id'],
            'status' => ucfirst($row['status']),
            'lokasi' => $row['lokasi_p'] ?? 'Lokasi Umum',
            'alamat' => $row['alamat'] ?? '-',
            'waktu'  => date('d M Y', strtotime($row['updated_at']))
        ];
    }
    echo json_encode($list);
    exit;
}

// Mengambil detail laporan (termasuk foto) untuk modal
if ($action == 'get_detail_tugas') {
    $id_tugas = $_GET['id'];
    
    // Ambil data dari tabel laporan
    $query = mysqli_query($conn, "SELECT * FROM laporan WHERE tugas_id = '$id_tugas'");
    $res = mysqli_fetch_assoc($query);
    
    if ($res) {
        echo json_encode([
            'foto' => $res['foto'] ?? 'default.jpg',
            'catatan' => $res['catatan'] ?? 'Tidak ada keterangan.',
            'tgl' => date('d M Y', strtotime($res['tanggal_selesai']))
        ]);
    } else {
        echo json_encode([
            'foto' => 'default.jpg',
            'catatan' => 'Data laporan belum dibuat.',
            'tgl' => '-'
        ]);
    }
    exit;
}

if ($action == 'get_tugas_teknisi') {
    $id_user = $_SESSION['user_id'];
    
    // Ambil ID teknisi berdasarkan user_id session
    $q_tek = mysqli_query($conn, "SELECT id FROM teknisi WHERE user_id = '$id_user'");
    $tek = mysqli_fetch_assoc($q_tek);
    $id_teknisi = $tek['id'];

    // Hitung statistik tugas milik teknisi tersebut
    $s_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tugas WHERE teknisi_id='$id_teknisi' AND status='pending'"))['t'];
    $s_proses  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tugas WHERE teknisi_id='$id_teknisi' AND status='proses'"))['t'];
    $s_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM tugas WHERE teknisi_id='$id_teknisi' AND status='selesai'"))['t'];

    // Ambil daftar tugas aktif (pending & proses)
    $query = mysqli_query($conn, "SELECT t.*, p.nama as lokasi_p, p.alamat 
                               FROM tugas t 
                               LEFT JOIN pelanggan p ON t.pelanggan_id = p.id 
                               WHERE t.teknisi_id = '$id_teknisi' AND t.status != 'selesai' 
                               ORDER BY t.prioritas DESC");
    $list = [];
    while($row = mysqli_fetch_assoc($query)) {
        $list[] = [
            'id'     => $row['id'],
            'status' => ucfirst($row['status']),
            'lokasi' => $row['lokasi_p'] ?? 'Lokasi Umum',
            'alamat' => $row['alamat'] ?? '-'
        ];
    }

    echo json_encode([
        'stats' => ['pending' => $s_pending, 'proses' => $s_proses, 'selesai' => $s_selesai],
        'list'  => $list
    ]);
    exit;
}

// Statistik Lengkap (Halaman Statistik)
if ($action == 'get_statistik_lengkap') {
    $bulan_ini = date('Y-m');
    
    // Total Tiket Selesai Bulan Ini
    $q_selesai = mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='selesai' AND DATE_FORMAT(updated_at, '%Y-%m') = '$bulan_ini'");
    $total_selesai = mysqli_fetch_assoc($q_selesai)['total'];

    // --- TAMBAHKAN INI: Tiket Selesai HARI INI ---
    $q_hari_ini = mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='selesai' AND DATE(updated_at) = CURDATE()");
    $total_hari_ini = mysqli_fetch_assoc($q_hari_ini)['total'];

    // Teknisi Terbaik Bulan Ini
    $q_best = mysqli_query($conn, "SELECT tk.nama, COUNT(t.id) as jumlah 
                                   FROM tugas t 
                                   JOIN teknisi tk ON t.teknisi_id = tk.id 
                                   WHERE t.status='selesai' AND DATE_FORMAT(t.updated_at, '%Y-%m') = '$bulan_ini'
                                   GROUP BY t.teknisi_id ORDER BY jumlah DESC LIMIT 1");
    $best_teknisi = mysqli_fetch_assoc($q_best)['nama'] ?? '-';

    // Statistik 7 Hari Terakhir
    $harian = [];
    for ($i = 6; $i >= 0; $i--) {
        $tgl = date('Y-m-d', strtotime("-$i days"));
        $label = date('d M', strtotime($tgl)); 
        $q_day = mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='selesai' AND DATE(updated_at) = '$tgl'");
        $harian[] = ['m' => $label, 'v' => (int)mysqli_fetch_assoc($q_day)['total']];
    }

    echo json_encode([
        'total_tiket' => $total_selesai, 
        'tiket_hari_ini' => $total_hari_ini, // Data baru dikirim ke JS
        'best_teknisi' => $best_teknisi, 
        'mingguan' => $harian
    ]);
    exit;
}

// Data Lokasi & Teknisi All
if ($action == 'get_lokasi_all') {
    $query = mysqli_query($conn, "SELECT * FROM pelanggan ORDER BY nama ASC");
    $data = [];
    while($row = mysqli_fetch_assoc($query)) { $data[] = $row; }
    echo json_encode($data);
    exit;
}

if ($action == 'get_teknisi_all') {
    $query = mysqli_query($conn, "SELECT * FROM teknisi ORDER BY nama ASC");
    $data = [];
    while($row = mysqli_fetch_assoc($query)) { $row['id_format'] = "TEK-0" . $row['id']; $data[] = $row; }
    echo json_encode($data);
    exit;
}

if ($action == 'submit_laporan_teknisi') {
    $tugas_id = $_POST['id'];
    $catatan  = mysqli_real_escape_string($conn, $_POST['catatan']);
    $id_user  = $_SESSION['user_id'];

    // Ambil ID teknisi asli dari user_id
    $q_tek = mysqli_query($conn, "SELECT id FROM teknisi WHERE user_id = '$id_user'");
    $id_teknisi = mysqli_fetch_assoc($q_tek)['id'];

    // Proses Upload Foto ke folder Storage
    $nama_file = "";
    if (isset($_FILES['foto'])) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nama_file = "LAP-" . time() . "." . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../Storage/" . $nama_file);
    }

    // 1. Masukkan data ke tabel laporan
    mysqli_query($conn, "INSERT INTO laporan (tugas_id, teknisi_id, catatan, foto, tanggal_selesai) 
                         VALUES ('$tugas_id', '$id_teknisi', '$catatan', '$nama_file', CURDATE())");

    // 2. Update status tugas menjadi 'selesai'
    mysqli_query($conn, "UPDATE tugas SET status='selesai', updated_at=NOW() WHERE id='$tugas_id'");

    echo json_encode(['status' => 'success']);
    exit;
}

// Dashboard Summary (Grafik & Kinerja)
if ($action == 'get_dashboard_all') {
    // Data Grafik 7 Hari Terakhir
    $grafik = [];
    for ($i = 6; $i >= 0; $i--) {
        // Mengambil tanggal mundur dari hari ini
        $date = date('Y-m-d', strtotime("-$i days"));
        $dayLabel = date('D', strtotime($date)); // Output: Mon, Tue, dsb.
        
        // Hitung tiket status selesai pada tanggal tersebut
        $q = mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='selesai' AND DATE(updated_at) = '$date'");
        $val = (int)mysqli_fetch_assoc($q)['total'];
        
        $grafik[] = ['day' => $dayLabel, 'val' => $val];
    }

    // 2. Data Kinerja Teknisi
    $teknisi = [];
    $q_tek = mysqli_query($conn, "SELECT id, nama FROM teknisi WHERE status='aktif'");
    while($t = mysqli_fetch_assoc($q_tek)) {
        $id_tek = $t['id'];
        $tugas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE teknisi_id='$id_tek' AND DATE(tanggal) = CURDATE()"))['total'];
        $selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE teknisi_id='$id_tek' AND status='selesai' AND DATE(tanggal) = CURDATE()"))['total'];
        $teknisi[] = ['nama' => $t['nama'], 'foto' => strtoupper(substr($t['nama'], 0, 1)), 'tugas' => (int)$tugas, 'selesai' => (int)$selesai];
    }

    // 3. Data Ringkasan Status (PENTING: Agar JS tidak error)
    $total_all = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas"))['total'] ?: 1;
    $s_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='pending'"))['total'];
    $s_proses = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='proses'"))['total'];
    $s_selesai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM tugas WHERE status='selesai'"))['total'];

    $ringkasan = [
        ['label' => 'Selesai', 'val' => (int)$s_selesai, 'total' => (int)$total_all, 'color' => 'var(--success)'],
        ['label' => 'Proses', 'val' => (int)$s_proses, 'total' => (int)$total_all, 'color' => 'var(--warning)'],
        ['label' => 'Pending', 'val' => (int)$s_pending, 'total' => (int)$total_all, 'color' => 'var(--danger)']
    ];

    header('Content-Type: application/json');
    echo json_encode(['grafik' => $grafik, 'teknisi' => $teknisi, 'ringkasan' => $ringkasan]);
    exit;
}

// --- BAGIAN POST DATA (TAMBAH, EDIT, UPDATE) ---

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // 1. Update Tiket Admin
    if ($action == 'update_tiket') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $teknisi_id = mysqli_real_escape_string($conn, $_POST['teknisi_id']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        mysqli_query($conn, "UPDATE tugas SET teknisi_id='$teknisi_id', status='$status', updated_at=NOW() WHERE id='$id'");
        header("Location: ../Resource/Views/admin/admin_tiket.php?status=updated");
        exit;
    }

// 2. Tambah Tiket Baru (Menggunakan Dropdown Pelanggan)
    if (isset($_POST['pelanggan_id']) && !isset($_POST['id']) && !isset($_POST['spesialis'])) {
        $teknisi_id   = $_POST['teknisi_id'];
        $pelanggan_id = $_POST['pelanggan_id']; 
        $priority     = $_POST['priority'];
        $tanggal      = date('Y-m-d');

        // Ambil nama pelanggan untuk dijadikan judul tiket agar tidak kosong di database
        $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM pelanggan WHERE id = '$pelanggan_id'"));
        $judul = "Perbaikan di " . ($res['nama'] ?? 'Lokasi');

        $sql_insert = "INSERT INTO tugas (teknisi_id, pelanggan_id, judul, tanggal, status, prioritas) 
                       VALUES ('$teknisi_id', '$pelanggan_id', '$judul', '$tanggal', 'pending', '$priority')";
        
        if(mysqli_query($conn, $sql_insert)) {
            header("Location: ../Resource/Views/admin/admin_tiket.php?status=success");
        } else {
            echo "Error SQL: " . mysqli_error($conn);
        }
        exit;
    }

    // 3. Edit Data Teknisi
    if (isset($_POST['id_teknisi'])) {
    $id = $_POST['id_teknisi'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']); // Update nomor HP
    $spec = mysqli_real_escape_string($conn, $_POST['spesialis']);
    $stat = strtolower($_POST['status']);
    
    mysqli_query($conn, "UPDATE teknisi SET nama='$nama', no_hp='$no_hp', spesialisasi='$spec', status='$stat' WHERE id='$id'");
    header("Location: ../Resource/Views/admin/admin_teknisi.php?status=success_update");
    exit;
}

    // 4. Tambah Teknisi Baru (Password Biasa)
    if (isset($_POST['spesialis']) && !isset($_POST['id_teknisi'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']); // Ambil input nomor HP
    $spesialis = mysqli_real_escape_string($conn, $_POST['spesialis']);
    $status = strtolower($_POST['status']);
    
    $username = strtolower(str_replace(' ', '', $nama));
    $pass = 'password123';

    // Simpan ke tabel users
    mysqli_query($conn, "INSERT INTO users (nama, username, password, role) VALUES ('$nama', '$username', '$pass', 'teknisi')");
    $user_id = mysqli_insert_id($conn);
    
    // Simpan ke tabel teknisi termasuk nomor HP
    mysqli_query($conn, "INSERT INTO teknisi (user_id, nama, no_hp, spesialisasi, status) 
                         VALUES ('$user_id', '$nama', '$no_hp', '$spesialis', '$status')");
                         
    header("Location: ../Resource/Views/admin/admin_teknisi.php?status=success_save");
    exit;
}

    // 5. Update Profil Teknisi (Password Biasa & Kolom Nama)
    if ($action == 'update_profil_teknisi') {
        $id_user = $_SESSION['user_id'];
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $pw_baru = $_POST['pw_baru'];
        $pw_konf = $_POST['pw_konfirmasi'];

        // Menyelaraskan nama kolom menjadi 'nama'
        mysqli_query($conn, "UPDATE users SET nama='$nama' WHERE id='$id_user'");
        $_SESSION['nama_lengkap'] = $nama;

        if (!empty($pw_baru) && $pw_baru === $pw_konf) {
            mysqli_query($conn, "UPDATE users SET password='$pw_baru' WHERE id='$id_user'"); // Password biasa
        }
        echo json_encode(['status' => 'success', 'message' => 'Profil berhasil diperbarui!']);
        exit;
    }

    // 6. Manajemen Lokasi
    if ($action == 'tambah_lokasi') {
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        mysqli_query($conn, "INSERT INTO pelanggan (nama, no_hp, alamat) VALUES ('$nama', '$no_hp', '$alamat')");
        header("Location: ../Resource/Views/admin/admin_lokasi.php?status=success");
        exit;
    }
    
    if ($action == 'update_lokasi') {
        $id = mysqli_real_escape_string($conn, $_POST['id']);
        $nama = mysqli_real_escape_string($conn, $_POST['nama']);
        $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
        $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
        mysqli_query($conn, "UPDATE pelanggan SET nama='$nama', no_hp='$no_hp', alamat='$alamat' WHERE id='$id'");
        header("Location: ../Resource/Views/admin/admin_lokasi.php?status=success_update");
        exit;
    }

    if (isset($_POST['pelanggan_id']) && !isset($_POST['id'])) {
    $teknisi_id   = $_POST['teknisi_id'];
    $pelanggan_id = $_POST['pelanggan_id']; // Diambil langsung dari dropdown
    $priority     = $_POST['priority'];
    $tanggal      = date('Y-m-d');

    // Ambil nama pelanggan untuk dijadikan judul tiket
    $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT nama FROM pelanggan WHERE id = '$pelanggan_id'"));
    $judul = "Perbaikan di " . ($res['nama'] ?? 'Lokasi');

    $sql_insert = "INSERT INTO tugas (teknisi_id, pelanggan_id, judul, tanggal, status, prioritas) 
                   VALUES ('$teknisi_id', '$pelanggan_id', '$judul', '$tanggal', 'pending', '$priority')";
    
    if(mysqli_query($conn, $sql_insert)) {
        header("Location: ../Resource/Views/admin/admin_tiket.php?status=success");
    } else {
        echo "Error SQL: " . mysqli_error($conn);
    }
    exit;
}
}

// --- BAGIAN DELETE (GET PARAMETER) ---
if ($action == 'delete_teknisi') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    mysqli_query($conn, "DELETE FROM teknisi WHERE id='$id'");
    header("Location: ../Resource/Views/admin/admin_teknisi.php?status=success_delete");
    exit;
}

if ($action == 'delete_lokasi') {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    mysqli_query($conn, "DELETE FROM pelanggan WHERE id='$id'");
    header("Location: ../Resource/Views/admin/admin_lokasi.php?status=success_delete");
    exit;
}
?>