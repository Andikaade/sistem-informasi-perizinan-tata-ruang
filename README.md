# Sistem Informasi Perizinan Tata Ruang

Aplikasi berbasis web yang dirancang untuk mengelola, mendokumentasikan, dan memonitoring seluruh proses pengajuan izin pemanfaatan tata ruang secara terintegrasi, transparan, dan efisien.

---

## 🚀 Fitur Utama
* **Manajemen Pengajuan Izin:** Alur pengajuan berkas perizinan dari pemohon secara sistematis.
* **Integrasi Mailtrap:** Notifikasi email otomatis untuk pembaruan status perizinan atau verifikasi.
* **Slicing UI Responsif:** Tampilan antarmuka yang bersih, modern, dan ramah pengguna di berbagai perangkat.
* **Halaman Kontak Kami:** Fitur interaktif bagi pengguna untuk mengirimkan pesan atau pertanyaan langsung ke admin.

## 🛠️ Teknologi yang Digunakan
* **Framework:** Laravel (PHP)
* **Frontend:** Blade Template Engine & Tailwind CSS / Bootstrap
* **Tools Pendukung:** Mailtrap (Pengujian Email)

---

## 📊 Arsitektur Database (Google Spreadsheet)

Proyek ini menggunakan **Google Spreadsheet** sebagai database utama untuk menyimpan data antrian, berkas pemohon, dan log status perizinan secara real-time. Keuntungannya meliputi kemudahan monitoring langsung dari Google Drive tanpa memerlukan DBMS tambahan seperti phpMyAdmin.

### ⚙️ Teknologi & Integrasi Google API
Untuk menghubungkan Laravel dengan Google Spreadsheet, proyek ini mengintegrasikan:
* **Google Cloud Console Service Account:** Autentikasi aman menggunakan file kredensial JSON (`credentials.json`).
* **Google Sheets API v4:** Membaca, memperbarui, dan menyisipkan baris data antrian secara asinkron.
* **Package Pendukung:** Google API Client Client Library untuk PHP.

---

## 💻 Cara Instalasi Lokal

### 1. Clone Repositori
Clone repositori ini terlebih dahulu ke direktori lokal Anda:
```bash
git clone [https://github.com/Andikaade/sistem-informasi-perizinan-tata-ruang.git](https://github.com/Andikaade/sistem-informasi-perizinan-tata-ruang.git)
