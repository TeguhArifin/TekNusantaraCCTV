<?php
session_start();
// Karena AuthController.php ada di app/Controllers/, 
// maka keluar satu folder untuk menemukan config.php di folder app/
require_once __DIR__ . "/../config.php"; 

class AuthController {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function login(string $username, string $password): void
    {
        if (empty($username) || empty($password)) {
            $this->redirectWithError('Username dan password tidak boleh kosong.');
            return;
        }

        $user = $this->findUserFromDatabase($username);

        // Verifikasi User dan Password (menggunakan password_hash)
        // Ganti baris password_verify lama menjadi perbandingan teks biasa (===)
if ($user && ($password === $user['password'])) {
    // Simpan data sesi
    $_SESSION['user_id']      = $user['id'];
    $_SESSION['username']     = $user['username'];
    $_SESSION['nama_lengkap'] = $user['nama']; // Sesuaikan dengan kolom 'nama' di SQL
    $_SESSION['role']         = $user['role'];
    $_SESSION['logged_in']    = true;

    $this->redirectByRole($user['role']);
} else {
    // Biarkan pesan error ini untuk sementara jika gagal
    die("DEBUG: Gagal! Input: $password | DB: " . $user['password']);
}
    }

    public function logout(): void
    {
        session_unset();
        session_destroy();
        header('Location: ../../Resource/Views/auth/login.php');
        exit;
    }

    // -------------------------------------------------------------------------
    // Query ke Database db_monitoring_teknisi
    // -------------------------------------------------------------------------
    private function findUserFromDatabase(string $username): ?array
{
    $username = mysqli_real_escape_string($this->db, $username);
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($this->db, $query);
    
    // DEBUG: Cek apakah user ketemu
    if (mysqli_num_rows($result) == 0) {
        // die("DEBUG: User $username tidak ditemukan di database!"); 
    }

    return mysqli_fetch_assoc($result);
}

    private function redirectByRole(string $role): void
    {
        $routes = [
            'admin'   => '../../Resource/Views/admin/admin_dashboard.php',
            'teknisi' => '../../Resource/Views/teknisi/teknisi_dashboard.php',
        ];

        $destination = $routes[$role] ?? null;

        if ($destination === null) {
            $this->redirectWithError('Role tidak dikenali.');
            return;
        }

        header("Location: $destination");
        exit;
    }

    private function redirectWithError(string $message): void
    {
        $_SESSION['error'] = $message;
        header('Location: ../../Resource/Views/auth/login.php');
        exit;
    }
}



// -------------------------------------------------------------------------
// Entry point menggunakan koneksi dari config.php
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan variabel $conn dari config.php tersedia
    $controller = new AuthController($conn);

    if (isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $controller->login($username, $password);
    }

    if (isset($_POST['logout'])) {
        $controller->logout();
    }
}

