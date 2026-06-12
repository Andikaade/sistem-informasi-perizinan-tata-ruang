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

## 💻 Cara Instalasi Lokal

1. **Clone repositori ini:**
```bash
   git clone [https://github.com/Andikaade/sistem-informasi-perizinan-tata-ruang.git](https://github.com/Andikaade/sistem-informasi-perizinan-tata-ruang.git)<img width="1782" height="875" alt="image" src="https://github.com/user-attachments/assets/8ababbda-5d0b-48e2-a5b2-5a9c6b8285d9" />

## 📊 Arsitektur Database (Google Spreadsheet)

Proyek ini menggunakan **Google Spreadsheet** sebagai database utama untuk menyimpan data antrian, berkas pemohon, dan log status perizinan secara real-time. Keuntungannya meliputi kemudahan monitoring langsung dari drive lokal tanpa memerlukan DBMS tambahan seperti phpMyAdmin.

### ⚙️ Teknologi & Integrasi Google API
Untuk menghubungkan Laravel dengan Google Spreadsheet, proyek ini mengintegrasikan:
* **Google Cloud Console Service Account:** Autentikasi aman menggunakan file kredensial JSON (`credentials.json`).
* **Google Sheets API v4:** Membaca, memperbarui, dan menyisipkan baris data antrian secara asinkron.
* **Package Pendukung:** `google/apiclient` atau `revolution/laravel-google-sheets` (sesuaikan dengan package yang Anda gunakan).

---

## 🛠️ Langkah Integrasi Google Spreadsheet

Jika Anda ingin mereplikasi atau memasang database ini di lingkungan lokal baru, ikuti langkah-langkah berikut:

### 1. Setup Google Cloud Console
1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Buat proyek baru (misal: `Petaru Sijunjung`).
3. Aktifkan **Google Sheets API** dan **Google Drive API** melalui menu API Library.
4. Masuk ke menu **Credentials**, lalu buat sebuah **Service Account**.
5. Masuk ke tab **Keys** pada Service Account tersebut, pilih **Add Key > Create New Key > JSON**. 
6. File JSON akan otomatis terunduh. Ganti namanya menjadi `credentials.json` dan letakkan di dalam folder `storage/app/google/` pada proyek Laravel Anda.

### 2. Konfigurasi Hak Akses Spreadsheet
1. Buat Google Spreadsheet baru di Google Drive Anda.
2. Buka file JSON `credentials.json` yang diunduh tadi, lalu salin alamat email service account yang tertera di properti `"client_email"`.
3. Klik tombol **Bagikan (Share)** pada Google Spreadsheet Anda, lalu tempelkan alamat email service account tersebut dan berikan hak akses sebagai **Editor**.
4. Salin **Spreadsheet ID** Anda dari URL browser:
   `https://docs.google.com/spreadsheets/d/TOTAL_ID_SPREADSHEET_ANDA/edit`

### 3. Tambahkan Variabel Environment (`.env`)
Buka file `.env` proyek Anda, kemudian tambahkan konfigurasi spreadsheet di bagian bawah untuk menghubungkan aplikasi:

```env
GOOGLE_APPLICATION_CREDENTIALS="${PATH_TO_STORAGE}/app/google/credentials.json"
GOOGLE_SPREADSHEET_ID=isi_dengan_spreadsheet_id_anda
GOOGLE_SHEET_NAME="Sheet1"
