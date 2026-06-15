// public/js/dashboard.js

document.addEventListener("DOMContentLoaded", function () {
    // 1. Inisialisasi Tooltip
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 2. Event Listener untuk Tombol Update Status (mengisi data ke Modal)
    document.querySelectorAll('.btn-update-status').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('modal_row_index').value = this.getAttribute('data-id');
            document.getElementById('modal_nama_pemohon').value = this.getAttribute('data-nama');
            document.getElementById('modal_no_surat').value = this.getAttribute('data-surat');
            document.getElementById('modal_status').value = this.getAttribute('data-status');
        });
    });

    // 3. Event Listener untuk Tombol Edit Data
    document.querySelectorAll('.btn-edit-data').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('edit_row_index').value = this.getAttribute('data-id');
            document.getElementById('edit_nama').value = this.getAttribute('data-nama');
            document.getElementById('edit_no_surat').value = this.getAttribute('data-surat');
        });
    });

    // 4. Event Listener untuk Tombol Lihat Detail (PERBAIKAN: Sekarang aman di dalam DOMContentLoaded)
    document.querySelectorAll('.btn-detail-data').forEach(btn => {
        btn.addEventListener('click', function() {
            // Ambil data dari atribut tombol
            const nama = this.getAttribute('data-nama');
            const surat = this.getAttribute('data-surat');
            const status = this.getAttribute('data-status');

            // Isi ke modal (Gunakan textContent untuk tag <td> / <span>)
            document.getElementById('detail_nama_pemohon').textContent = nama;
            document.getElementById('detail_no_surat').textContent = surat;
            document.getElementById('detail_status').textContent = status;

            // Buka modal secara paksa
            const modalElement = document.getElementById('detailDataModal');
            if (modalElement) {
                const myModal = new bootstrap.Modal(modalElement);
                myModal.show();
            }
        });
    });

    // 5. Filter Tabel
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('perizinanTable');

    if (searchInput && statusFilter && table) {
        function filterTable() {
            const textKeyword = searchInput.value.toLowerCase().trim();
            const selectedStatus = statusFilter.value.toLowerCase().trim();
            const tr = table.getElementsByTagName('tr');

            for (var i = 1; i < tr.length; i++) {
                var tds = tr[i].getElementsByTagName('td');
                if (tds.length < 5) continue;
                
                var namaText = (tds[1].textContent || tds[1].innerText).toLowerCase();
                var suratText = (tds[2].textContent || tds[2].innerText).toLowerCase();
                var statusText = (tds[3].textContent || tds[3].innerText).toLowerCase();

                const matchesText = textKeyword === "" || namaText.includes(textKeyword) || suratText.includes(textKeyword);
                const matchesStatus = selectedStatus === "" || statusText.includes(selectedStatus);

                tr[i].style.display = (matchesText && matchesStatus) ? "" : "none";
            }
        }
        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);
    }
});

// Fungsi untuk konfirmasi hapus (tetap di luar / pakai objek window)
window.konfirmasiHapus = function(event) {
    if (!confirm("Apakah Anda yakin ingin menghapus data perizinan ini?")) {
        event.preventDefault(); 
        return false;
    }
    return true;
};