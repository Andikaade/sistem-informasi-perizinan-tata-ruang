// =========================================================================
// HELPER FUNCTION: Mengubah SEGALA FORMAT tanggal dari aplikasi ke Indo
// =========================================================================
function formatTanggalIndonesia(dateString) {
    if (!dateString || dateString.trim() === '-' || dateString.trim() === '') return '-';
    
    const bulanIndo = {
        'jan': 'Januari', 'feb': 'Februari', 'mar': 'Maret', 'apr': 'April',
        'may': 'Mei', 'jun': 'Juni', 'jul': 'Juli', 'aug': 'Agustus',
        'sep': 'September', 'oct': 'Oktober', 'nov': 'November', 'dec': 'Desember'
    };

    let cleanString = dateString.trim();

    // Skenario 1: Jika format berisi tanda miring (Hasil dari updateData / Edit -> Contoh: 18/06/2026 10:15)
    if (cleanString.includes('/')) {
        const parts = cleanString.split(' '); 
        const dateParts = parts[0].split('/');
        
        if (dateParts.length === 3) {
            const tgl = dateParts[0];
            const namaBulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ][parseInt(dateParts[1]) - 1];
            const thn = dateParts[2];
            const jam = parts[1] ? ' ' + parts[1] : ''; // Pertahankan jam menit log edit
            
            return `${tgl} ${namaBulan} ${thn}${jam}`;
        }
    }

    // Skenario 2: Jika format berisi tanda strip text (Hasil dari alur utama -> Contoh: 17-Jun-2026)
    if (cleanString.includes('-')) {
        const parts = cleanString.split(' ');
        const datePart = parts[0];
        const timePart = parts[1] || '';

        const dateSplit = datePart.split('-');
        if (dateSplit.length === 3) {
            const tgl = dateSplit[0];
            const blnRaw = dateSplit[1].toLowerCase();
            const thn = dateSplit[2];
            
            const namaBulan = bulanIndo[blnRaw] || dateSplit[1]; 
            const jam = timePart ? ' ' + timePart : '';
            
            return `${tgl} ${namaBulan} ${thn}${jam}`;
        }
    }

    return dateString;
}

document.addEventListener("DOMContentLoaded", function () {
    // Variable Global untuk validasi Captcha Matematika Sederhana
    let angkaUtama1 = 0;
    let angkaUtama2 = 0;
    let hasilKunciCaptcha = 0;

    function buatCaptchaBaru() {
        angkaUtama1 = Math.floor(Math.random() * 10) + 1; // Angka acak 1 - 10
        angkaUtama2 = Math.floor(Math.random() * 10) + 1; // Angka acak 1 - 10
        hasilKunciCaptcha = angkaUtama1 + angkaUtama2;
        
        const elCaptchaText = document.getElementById('captcha_text');
        if (elCaptchaText) {
            elCaptchaText.textContent = `${angkaUtama1} + ${angkaUtama2}`;
        }
        
        const elInputCaptcha = document.getElementById('edit_captcha_input');
        if (elInputCaptcha) elInputCaptcha.value = '';
        
        const elErrorMsg = document.getElementById('captcha_error_msg');
        if (elErrorMsg) elErrorMsg.classList.add('d-none');
    }

    // Refresh captcha manual lewat tombol reload
    const btnRefresh = document.getElementById('btn_refresh_captcha');
    if (btnRefresh) {
        btnRefresh.addEventListener('click', buatCaptchaBaru);
    }

    // 1. Inisialisasi Tooltip Bootstrap (Bawaan)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // =========================================================================
    // EVENT DELEGATION: Deteksi Klik Tombol Aksi di Tabel
    // =========================================================================
    document.addEventListener('click', function (event) {
        
        // ---------------------------------------------------------------------
        // A. KONDISI: TOMBOL LIHAT DETAIL DIKLIK
        // ---------------------------------------------------------------------
        const btnDetail = event.target.closest('.btn-detail-data');
        
        if (btnDetail) {
            event.preventDefault();

            const antrian      = btnDetail.getAttribute('data-antrian') || '-';
            const nama         = btnDetail.getAttribute('data-nama') || '-';
            const surat        = btnDetail.getAttribute('data-surat') || '-';
            const deskripsi    = btnDetail.getAttribute('data-deskripsi') || '-';
            const phone        = btnDetail.getAttribute('data-phone') || '-';
            const alamat       = btnDetail.getAttribute('data-alamat') || '-';
            const statusText   = btnDetail.getAttribute('data-status') || 'Pending';
            
            // Format Alur Utama berkas
            const tglPengajuan = formatTanggalIndonesia(btnDetail.getAttribute('data-tgl-pengajuan'));
            const tglProses    = formatTanggalIndonesia(btnDetail.getAttribute('data-tgl-proses'));
            const tglSelesai   = formatTanggalIndonesia(btnDetail.getAttribute('data-tgl-selesai'));
            
            // Log Perubahan Data
            const createdBy    = btnDetail.getAttribute('data-created-by') || '-';
            const tglUpdate    = formatTanggalIndonesia(btnDetail.getAttribute('data-tgl-update'));
            const updatedBy    = btnDetail.getAttribute('data-update-by') || '-'; 

            const elements = {
                'detail_no_antrian': antrian,
                'detail_nama_pemohon': nama,
                'detail_no_surat': surat,
                'detail_deskripsi_surat': deskripsi,
                'detail_phone': phone,
                'detail_alamat': alamat,
                'detail_tgl_pengajuan': tglPengajuan,
                'detail_tgl_proses': tglProses,
                'detail_tgl_selesai': tglSelesai,
                'detail_created_by': createdBy,
                'detail_tgl_update': tglUpdate,
                'detail_updated_by': updatedBy
            };

            Object.keys(elements).forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.textContent = elements[id];
                }
            });
            
            // Manajemen Badge Warna Status
            const statusElement = document.getElementById('detail_status');
            if (statusElement) {
                statusElement.textContent = statusText.toUpperCase();
                statusElement.className = "badge"; 
                
                const statusLower = statusText.toLowerCase().trim();
                if (statusLower === 'selesai' || statusLower === 'sukses') {
                    statusElement.classList.add('bg-success');
                } else if (statusLower === 'proses' || statusLower === 'pending' || statusLower === 'dalam proses') {
                    statusElement.classList.add('bg-warning', 'text-dark');
                } else if (statusLower === 'dikembalikan') {
                    statusElement.classList.add('bg-danger');
                } else {
                    statusElement.classList.add('bg-secondary');
                }
            }

            const modalElement = document.getElementById('detailDataModal');
            if (modalElement) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
            return;
        }

        // ---------------------------------------------------------------------
        // B. KONDISI: TOMBOL UPDATE STATUS DIKLIK
        // ---------------------------------------------------------------------
        const btnUpdateStatus = event.target.closest('.btn-update-status');
        
        if (btnUpdateStatus) {
            event.preventDefault();

            const rowIndex   = btnUpdateStatus.getAttribute('data-index');
            const antrian    = btnUpdateStatus.getAttribute('data-antrian') || '-';
            const nama       = btnUpdateStatus.getAttribute('data-nama') || '-';
            const surat      = btnUpdateStatus.getAttribute('data-surat') || '-';
            const currentStatus = btnUpdateStatus.getAttribute('data-status') || 'pending';

            const elRowIndex = document.getElementById('modal_row_index');
            const elAntrian  = document.getElementById('status_no_antrian');
            const elNama     = document.getElementById('modal_nama_pemohon');
            const elSurat    = document.getElementById('modal_no_surat');
            const elSelect   = document.getElementById('modal_select_status');

            if (elRowIndex) elRowIndex.value = rowIndex;
            if (elAntrian)  elAntrian.value = antrian;
            if (elNama)     elNama.value = nama;
            if (elSurat)    elSurat.value = surat;

            if (elSelect) {
                const statusLower = currentStatus.toLowerCase().trim();
                if (statusLower === 'selesai' || statusLower === 'sukses') {
                    elSelect.value = 'selesai';
                } else if (statusLower === 'dikembalikan') {
                    elSelect.value = 'dikembalikan';
                } else {
                    elSelect.value = 'proses';
                }
            }

            const modalElement = document.getElementById('updateStatusModal');
            if (modalElement) {
                bootstrap.Modal.getOrCreateInstance(modalElement).show();
            }
            return;
        }

        // ---------------------------------------------------------------------
        // C. KONDISI: TOMBOL EDIT DATA DIKLIK
        // ---------------------------------------------------------------------
        const btnEdit = event.target.closest('.btn-edit-data');
        
        if (btnEdit) {
            event.preventDefault();
            
            const elEditId        = document.getElementById('edit_row_index');
            const elEditAntrian   = document.getElementById('edit_no_antrian');
            const elEditNama      = document.getElementById('edit_nama');
            const elEditSurat     = document.getElementById('edit_no_surat');
            const elEditDeskripsi = document.getElementById('edit_deskripsi');
            const elEditPhone     = document.getElementById('edit_phone');
            const elEditAlamat    = document.getElementById('edit_alamat');

            if (elEditId)        elEditId.value        = btnEdit.getAttribute('data-index') || '';
            if (elEditAntrian)   elEditAntrian.value   = btnEdit.getAttribute('data-antrian') || '';
            if (elEditNama)      elEditNama.value      = btnEdit.getAttribute('data-nama') || '';
            if (elEditSurat)     elEditSurat.value     = btnEdit.getAttribute('data-surat') || '';
            if (elEditDeskripsi) elEditDeskripsi.value = btnEdit.getAttribute('data-deskripsi') || '';
            if (elEditPhone)     elEditPhone.value     = btnEdit.getAttribute('data-phone') || '';
            if (elEditAlamat)    elEditAlamat.value    = btnEdit.getAttribute('data-alamat') || '';
            
            buatCaptchaBaru();

            const editModalEl = document.getElementById('editDataModal');
            if (editModalEl) {
                bootstrap.Modal.getOrCreateInstance(editModalEl).show();
            }
            return;
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

    // =========================================================================
    // VERIFIKASI CLIENT-SIDE & ANIMASI LOADING SAAT SUBMIT
    // =========================================================================
    const formEdit = document.getElementById('formEditData');
    if (formEdit) {
        formEdit.addEventListener('submit', function (event) {
            const inputUserCaptcha = parseInt(document.getElementById('edit_captcha_input').value);
            const elErrorMsg = document.getElementById('captcha_error_msg');

            if (isNaN(inputUserCaptcha) || inputUserCaptcha !== hasilKunciCaptcha) {
                event.preventDefault(); 
                if (elErrorMsg) elErrorMsg.classList.remove('d-none');
                return false;
            }

            if (elErrorMsg) elErrorMsg.classList.add('d-none');

            const btnSubmit = formEdit.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Menyimpan...
                `;
            }
        });
    }

    const formUpdateStatus = document.querySelector('#updateStatusModal form');
    if (formUpdateStatus) {
        formUpdateStatus.addEventListener('submit', function () {
            const btnSubmit = formUpdateStatus.querySelector('button[type="submit"]');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Menyimpan...
                `;
            }
        });
    }

 // =========================================================================
    // PROSES SIMPAN DATA PERIZINAN BARU (AJAX) - BERSIH & AMAN
    // =========================================================================
    const formTambah = document.getElementById('formTambahPerizinan');
    if (formTambah) {
        formTambah.addEventListener('submit', function (event) {
            event.preventDefault();
            const form = this;

            if (!form.checkValidity()) {
                event.stopPropagation();
                form.classList.add('was-validated');
                return false;
            }

            const btnSubmit = form.querySelector('button[type="submit"]');
            const originalText = btnSubmit.innerHTML;
            
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Menyimpan...
            `;

            const formData = $(form).serialize();

            $.ajax({
    url: '/dashboard/perizinan/store',
    type: 'POST',
    data: formData, // Sesuaikan dengan variabel data form kamu
    headers: {
        'Accept': 'application/json', // <--- WAJIB TAMBAHKAN BARIS INI
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
        // Handle jika sukses
        alert('Data berhasil disimpan!');
        location.reload();
    },
    error: function(xhr) {
        console.error(xhr.responseText); // <--- Membantu trace error
        
        // Cek jika respons berupa JSON error dari validasi Laravel
        if (xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            // Handle tampilan error validasi di form kamu...
        } else {
            alert('Gagal menyimpan data. Periksa Network tab atau log server.');
        }
    }
});
        });
    }

    // =========================================================================
    // OTOMATISASI GENERATE NOMOR ANTRIAN
    // =========================================================================
    const modalTambahEl = document.getElementById('modalTambahPerizinan');
    if (modalTambahEl) {
        modalTambahEl.addEventListener('hidden.bs.modal', function () {
            if (formTambah) {
                formTambah.reset();
                formTambah.classList.remove('was-validated');
                const invalidInputs = formTambah.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => input.classList.remove('is-invalid'));
            }
        });

        modalTambahEl.addEventListener('show.bs.modal', function () {
            const inputAntrian = document.getElementById('add_no_antrian');
            if (inputAntrian) {
                inputAntrian.value = 'Memuat nomor...'; 
            }

            $.ajax({
                url: "/dashboard/perizinan/generate-antrian", 
                type: "GET",
                dataType: "json",
                success: function (response) {
                    if (response.success && inputAntrian) {
                        inputAntrian.value = response.no_antrian;
                    }
                },
                error: function (xhr) {
                    if (inputAntrian) {
                        inputAntrian.value = '';
                        inputAntrian.placeholder = 'Gagal memuat otomatis, silakan ketik manual';
                        inputAntrian.removeAttribute('readonly');
                        inputAntrian.classList.remove('bg-light');
                    }
                    console.error("Gagal mendapatkan nomor antrian berkas:", xhr.responseText);
                }
            });
        });
    }
});

// Fungsi Konfirmasi Hapus Data (Global Scope)
window.konfirmasiHapus = function(event) {
    if (!confirm("Apakah Anda yakin ingin menghapus data perizinan ini?")) {
        event.preventDefault(); 
        return false;
    }
    return true;
};
