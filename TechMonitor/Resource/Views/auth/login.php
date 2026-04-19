<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-5 col-lg-4">
            <div class="text-center mb-4">
                <h2 class="fw-bold text-primary">TechMonitor</h2>
                <p class="text-muted">Sistem Monitoring Kinerja Teknisi</p>
            </div>
            
            <div class="card login-card">
                <div class="card-body p-4">
                    <form action="../../../app/controllers/AuthController.php" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label text-secondary">Username</label>
                            <input type="text" name="username" class="form-control form-control-lg" id="username" placeholder="Masukkan username" required autofocus>
                        </div>
                        
                        <div class="mb-4">
                            <label for="password" class="form-label text-secondary">Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" id="password" placeholder="••••••••" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="login" class="btn btn-primary btn-lg">Masuk ke Sistem</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-muted small">
                &copy; 2026 PT. Nusantara CCTV - All Rights Reserved
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>