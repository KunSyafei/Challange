# 📚 Student Monitoring System - README (Simple Version)

## Tentang Aplikasi

Sistem untuk memonitor kegiatan siswa (Notulensi, Mentoring, Kehadiran, Nilai Bina Jasmani, dll)

## Login

| Role | Username | Password |
|------|----------|----------|
| Admin | `admin` | `admin123` |
| Super Admin | `superadmin` | `super123` |

## Cara Install (3 Langkah)

### 1. Persiapan
- Install **XAMPP** (buka Apache & MySQL)
- Copy folder `student-monitoring` ke `C:\xampp\htdocs\`

### 2. Setup Database
- Buka `http://localhost/phpmyadmin`
- Buat database: `student_monitoring`
- Import file `database.sql`

### 3. Jalankan
- Akses: `http://localhost/student-monitoring`

## Struktur Folder

```
student-monitoring/
├── assets/         # CSS, gambar
├── config/         # Konfigurasi database
├── api/           # API backend
├── pages/         # Halaman web
├── index.php      # Login
└── dashboard.php  # Dashboard utama
```

## Fitur

| Menu | Fungsi |
|------|--------|
| Notulensi Rabuan | Upload PDF hasil rapat |
| Mentoring | Kelola materi & pengisi |
| Timeline | Jadwal kegiatan |
| Pra Operasional | Persiapan kegiatan |
| Operasional | Upload laporan kegiatan |
| Pasca Operasional | Cek peralatan |
| Bina Jasmani | Input nilai latihan |
| Kehadiran | Absensi siswa |
| Virtualisasi | Grafik data siswa |

## Error & Solusi

| Error | Solusi |
|-------|--------|
| Database not found | Buat database `student_monitoring` di phpMyAdmin |
| Failed to open stream | Cek path file, pastikan folder `pages/` menggunakan `../config/database.php` |
| CSS tidak muncul | Refresh browser (Ctrl+F5) |

## Catatan

- File upload disimpan di `assets/uploads/`
- Pastikan folder `uploads` bisa ditulis (writable)

---

**Selesai! Buka `http://localhost/student-monitoring` untuk mulai menggunakan.**

<img width="1365" height="635" alt="Cuplikan layar 2026-05-21 201104" src="https://github.com/user-attachments/assets/7caa350c-ebac-428a-ad99-ad41fa6efe87" />
<img width="1365" height="627" alt="Cuplikan layar 2026-05-21 201122" src="https://github.com/user-attachments/assets/53c53ebe-3ea7-42a4-8027-38d62472631e" />
<img width="1365" height="628" alt="image" src="https://github.com/user-attachments/assets/ec47f38e-7560-46a4-b49f-887d8beb3d8e" />
<img width="1365" height="634" alt="image" src="https://github.com/user-attachments/assets/8e7b2819-fc2b-435e-91a4-98e020019de8" />


