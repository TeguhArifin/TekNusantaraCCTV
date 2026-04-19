<div class="modal-body p-4">
    <form action="proses_tambah_tiket.php" method="POST">
        <div class="mb-4">
            <label class="form-label small fw-bold text-white">Nama Teknisi</label>
            <select name="teknisi_id" class="form-select bg-transparent text-white border-secondary border-opacity-50" style="font-size: 0.875rem;" required>
                <option value="" class="bg-dark">Pilih Teknisi</option>
                <option value="1" class="bg-dark">Budi Santoso</option>
                <option value="2" class="bg-dark">Andi Prasetyo</option>
                <option value="3" class="bg-dark">Rizky Firmansyah</option>
            </select>
        </div>
        
        <div class="mb-4">
            <label class="form-label small fw-bold text-white">Lokasi Perbaikan</label>
            <input type="text" 
                   name="lokasi" 
                   class="form-control bg-transparent text-white border-secondary border-opacity-50 custom-placeholder" 
                   placeholder="Contoh: Gedung B Lt.2" 
                   style="font-size: 0.875rem;" required>
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