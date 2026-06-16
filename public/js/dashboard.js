// public/js/dashboard.js

document.addEventListener("DOMContentLoaded", function () {
    // 1. Inisialisasi Tooltip Bootstrap (Bawaan)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // =========================================================================
    // EVENT DELEGATION: Mengamankan tombol Detail, Update Status, dan Edit
    // =========================================================================
    document.addEventListener('click', function (event) {
        // Deteksi tombol atau icon bagian dalam tombol yang sedang diklik
        const btnDetail = event.target.closest('.btn-detail-data');
        const btnUpdate = event.target.closest('.btn-update-status');
        const btnEdit   = event.target.closest('.btn-edit-data');

        // ---------------------------------------------------------------------
        // A. KONDISI: TOMBOL LIHAT DETAIL DIKLIK
        // ---------------------------------------------------------------------
        if (btnDetail) {
            event.preventDefault();

            // Mengambil data dari atribut HTML dengan fallback tanda strip (-) jika kosong
            const antrian      = btnDetail.getAttribute('data-antrian') || '-';
            const nama         = btnDetail.getAttribute('data-nama') || '-';
            const surat        = btnDetail.getAttribute('data-surat') || '-';
            const deskripsi    = btnDetail.getAttribute('data-deskripsi') || '-';
            const phone        = btnDetail.getAttribute('data-phone') || '-';
            const alamat       = btnDetail.getAttribute('data-alamat') || '-';
            const tglPengajuan = btnDetail.getAttribute('data-tgl-pengajuan') || '-';
            const tglProses    = btnDetail.getAttribute('data-tgl-proses') || '-';
            const tglSelesai   = btnDetail.getAttribute('data-tgl-selesai') || '-';
            const statusText   = btnDetail.getAttribute('data-status') || 'Pending';

            // Pemetaan ID Elemen Modal sesuai dengan struktur tabel HTML Blade Anda
            const elements = {
                'detail_no_antrian': antrian,
                'detail_nama_pemohon': nama,
                'detail_no_surat': surat,
                'detail_deskripsi_surat': deskripsi,
                'detail_phone': phone,
                'detail_alamat': alamat,
                'detail_tgl_pengajuan': tglPengajuan,
                'detail_tgl_proses': tglProses,
                'detail_tgl_selesai': tglSelesai
            };

            // Mengisi data teks ke elemen modal secara aman (menghindari Uncaught TypeError)
            Object.keys(elements).forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = elements[id];
                }
            });
            
            // Khusus STATUS PERIZINAN: Mengubah Teks & Warna Badge secara Dinamis
            const statusElement = document.getElementById('detail_status');
            if (statusElement) {
                statusElement.textContent = statusText.toUpperCase();
                statusElement.className = "badge"; // Reset class styling dasar bootstrap
                
                const statusLower = statusText.toLowerCase().trim();
                if (statusLower === 'selesai') {
                    statusElement.classList.add('bg-success');
                } else if (statusLower === 'dalam proses' || statusLower === 'pending') {
                    statusElement.classList.add('bg-warning', 'text-dark');
                } else if (statusLower === 'dikembalikan') {
                    statusElement.classList.add('bg-danger');
                } else {
                    statusElement.classList.add('bg-secondary');
                }
            }

            // Membuka modal detail
            const modalElement = document.getElementById('detailDataModal');
            if (modalElement) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
        }

        // ---------------------------------------------------------------------
        // B. KONDISI: TOMBOL UPDATE STATUS DIKLIK
        // ---------------------------------------------------------------------
        if (btnUpdate) {
            event.preventDefault();
            
            const elId     = document.getElementById('modal_row_index');
            const elNama   = document.getElementById('modal_nama_pemohon');
            const elSurat  = document.getElementById('modal_no_surat');
            const elStatus = document.getElementById('modal_status');

            if (elId)     elId.value     = btnUpdate.getAttribute('data-id') || '';
            if (elNama)   elNama.value   = btnUpdate.getAttribute('data-nama') || '';
            if (elSurat)  elSurat.value  = btnUpdate.getAttribute('data-surat') || '';
            if (elStatus) elStatus.value = btnUpdate.getAttribute('data-status') || '';
            
            const updateModalEl = document.getElementById('updateStatusModal');
            if (updateModalEl) {
                bootstrap.Modal.getOrCreateInstance(updateModalEl).show();
            }
        }

        // ---------------------------------------------------------------------
        // C. KONDISI: TOMBOL EDIT DATA DIKLIK
        // ---------------------------------------------------------------------
        if (btnEdit) {
            event.preventDefault();
            
            const elEditId    = document.getElementById('edit_row_index');
            const elEditNama  = document.getElementById('edit_nama');
            const elEditSurat = document.getElementById('edit_no_surat');

            if (elEditId)    elEditId.value    = btnEdit.getAttribute('data-id') || '';
            if (elEditNama)  elEditNama.value  = btnEdit.getAttribute('data-nama') || '';
            if (elEditSurat) elEditSurat.value = btnEdit.getAttribute('data-surat') || '';
            
            const editModalEl = document.getElementById('editDataModal');
            if (editModalEl) {
                bootstrap.Modal.getOrCreateInstance(editModalEl).show();
            }
        }
    });

    // =========================================================================
    // 5. Fitur Pencarian & Filter Tabel Dashboard
    // =========================================================================
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
                
                var antrianText = (tds[1].textContent || tds[1].innerText).toLowerCase();
                var namaText    = (tds[2].textContent || tds[2].innerText).toLowerCase();
                var suratText   = (tds[3].textContent || tds[3].innerText).toLowerCase();
                var statusText  = (tds[4].textContent || tds[4].innerText).toLowerCase();

                const matchesText = textKeyword === "" || 
                                    antrianText.includes(textKeyword) || 
                                    namaText.includes(textKeyword) || 
                                    suratText.includes(textKeyword);
                                    
                const matchesStatus = selectedStatus === "" || statusText.includes(selectedStatus);

                tr[i].style.display = (matchesText && matchesStatus) ? "" : "none";
            }
        }
        searchInput.addEventListener('keyup', filterTable);
        statusFilter.addEventListener('change', filterTable);
    }
});

// Fungsi Konfirmasi Hapus Data
window.konfirmasiHapus = function(event) {
    if (!confirm("Apakah Anda yakin ingin menghapus data perizinan ini?")) {
        event.preventDefault(); 
        return false;
    }
    return true;
};