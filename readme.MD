# 📄 Aplikasi PPID Bulungan

Aplikasi PPID (Pejabat Pengelola Informasi dan Dokumentasi) Bulungan adalah sistem manajemen permohonan informasi publik berbasis web yang dikembangkan menggunakan PHP dan MySQL. Aplikasi ini mendukung fitur CRUD, login user, upload dokumen, pengelolaan laporan, dan ekspor data ke PDF.

---

## 🚀 Fitur Utama

- Autentikasi pengguna (login & logout)
- Tambah/Edit/Hapus permohonan informasi
- Upload **KTP (.jpg)** dan **Surat Kuasa (.pdf)** (opsional)
- Status permohonan: `Belum Diproses`, `Sudah Diproses`, `Selesai`, `Ditolak`
- Dashboard dengan filter **bulan**, **tahun**, dan **status**
- Ekspor laporan ke PDF berdasarkan filter
- Cek status permohonan tanpa login
- Penyimpanan file berdasarkan folder `uploads/tahun/bulan`
- Tampilan berbasis **Bootstrap 5**, sudah responsif

---

## 🗂 Struktur Folder

```plaintext
/
├── assets/
│   └── img/                # Logo, ikon, kop surat
├── config/
│   └── database.php        # Konfigurasi koneksi DB
├── includes/
│   ├── auth_check.php      # Middleware login
│   └── functions.php       # Fungsi utilitas
├── lib/
│   └── dompdf/             # Library PDF (manual install)
├── public/
│   ├── login.php
│   ├── logout.php
│   ├── dashboard.php
│   ├── tambah.php
│   ├── edit.php
│   ├── detail.php
│   ├── laporan.php
│   ├── export.php
│   └── cek_status.php
├── uploads/
│   └── [tahun]/[bulan]/    # Lokasi upload file
├── favicon.ico
└── README.md
