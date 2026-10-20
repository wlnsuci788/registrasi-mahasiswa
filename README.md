# Portal Registrasi Mahasiswa Asing – Universitas Andalas

> Aplikasi berbasis web untuk mengelola proses registrasi calon mahasiswa asing dan WNI, mulai dari registrasi akun, pengisian formulir, pengunggahan dokumen, verifikasi atau seleksi, hingga penerbitan registration number.

## 📋 Tentang Proyek

Portal Registrasi Mahasiswa Asing – Universitas Andalas merupakan aplikasi berbasis web yang dirancang untuk membantu proses pendaftaran calon mahasiswa secara terstruktur.

Sistem mendukung pendaftaran mahasiswa Warga Negara Asing (WNA) dan Warga Negara Indonesia (WNI), termasuk pengisian data diri, pemilihan program, pengunggahan dokumen persyaratan, serta pemantauan status pendaftaran.

Sistem melibatkan lima aktor utama:

- **Calon Mahasiswa** — membuat akun, mengisi pendaftaran, mengunggah dokumen, dan memantau status.
- **International Office** — mengelola dokumen persyaratan, memverifikasi pendaftaran mahasiswa asing, dan memproses bantuan visa.
- **UPT Pendidikan Luar Kampus (PDK)** — menyeleksi pendaftar WNI.
- **Fakultas** — memeriksa pendaftar mahasiswa asing sesuai fakultas terkait dan memberikan usulan keputusan.
- **Akademik** — menerbitkan registration number bagi pendaftar yang telah lolos.

## ✨ Fitur Utama

### 👤 Calon Mahasiswa
- Registrasi akun dan login.
- Mengisi formulir pendaftaran secara bertahap.
- Mengisi data diri dan memilih program pendaftaran.
- Memilih program degree atau non-degree sesuai ketentuan.
- Memilih fakultas tujuan.
- Mengisi data akademik atau lama kunjungan sesuai program.
- Mengunggah dokumen pendukung.
- Memilih pengurusan visa secara mandiri atau dengan bantuan.
- Mengirim pendaftaran setelah seluruh tahapan lengkap.
- Memantau status pendaftaran dan bantuan visa.

### 🌐 International Office
- Login sebagai pengguna internal.
- Mengelola dokumen persyaratan.
- Memverifikasi pendaftaran mahasiswa asing.
- Memberikan keputusan lolos atau tidak lolos beserta catatan.
- Memproses bantuan pengurusan visa.
- Memperbarui status dan catatan pengurusan visa.

### 🏢 UPT Pendidikan Luar Kampus (PDK)
- Login sebagai pengguna internal.
- Melihat daftar pendaftar WNI.
- Memeriksa data dan dokumen pendaftar.
- Menetapkan keputusan seleksi beserta catatan.
- Meneruskan pendaftar yang lolos ke Akademik.

### 🏫 Fakultas
- Login sebagai pengguna internal.
- Melihat pendaftar mahasiswa asing sesuai fakultas terkait.
- Memeriksa data dan dokumen pendaftar.
- Memberikan usulan keputusan beserta catatan.

### 🎓 Akademik
- Login sebagai pengguna internal.
- Melihat daftar pendaftar yang telah lolos.
- Memeriksa kelengkapan data pendaftar.
- Menerbitkan registration number yang unik.
- Memperbarui status pendaftaran setelah nomor diterbitkan.

## 🗂️ Aktor Sistem

| Aktor | Akses | Deskripsi |
|---|---|---|
| Calon Mahasiswa | Registrasi akun dan login | Mengisi pendaftaran dan memantau status miliknya. |
| International Office | Login wajib | Mengelola persyaratan, verifikasi pendaftar asing, dan bantuan visa. |
| UPT Pendidikan Luar Kampus (PDK) | Login wajib | Menyeleksi pendaftar WNI. |
| Fakultas | Login wajib | Memeriksa pendaftar asing dan memberikan usulan keputusan. |
| Akademik | Login wajib | Menerbitkan registration number bagi pendaftar yang lolos. |

## 🔄 Alur Registrasi

1. Calon mahasiswa membuat akun dan login.
2. Calon mahasiswa mengisi data diri dan memilih program.
3. Sistem menampilkan isian lanjutan sesuai jalur dan program.
4. Calon mahasiswa mengisi data akademik atau lama kunjungan.
5. Calon mahasiswa mengunggah dokumen pendukung.
6. Calon mahasiswa memilih opsi pengurusan visa.
7. Calon mahasiswa memeriksa ringkasan dan mengirim pendaftaran.
8. Pendaftaran WNA diteruskan ke International Office, sedangkan pendaftaran WNI diteruskan ke UPT Pendidikan Luar Kampus.
9. Pendaftaran diperiksa dan diputuskan sesuai kewenangan masing-masing aktor.
10. Pendaftar yang lolos diteruskan ke Akademik.
11. Akademik menerbitkan registration number.
12. Calon mahasiswa memantau status pendaftaran melalui sistem.

## 📌 Status Pendaftaran

| Status | Keterangan |
|---|---|
| Draft | Pendaftaran masih dalam tahap pengisian. |
| Terkirim | Pendaftaran sudah dikirim untuk diproses. |
| Lolos | Pendaftar dinyatakan lolos. |
| Tidak lolos | Pendaftar dinyatakan tidak lolos. |
| Registration number terbit | Nomor registrasi telah diterbitkan oleh Akademik. |

### Status Pengurusan Visa

| Status | Keterangan |
|---|---|
| Menunggu diproses | Pengajuan menunggu penanganan. |
| Diproses | Pengajuan sedang ditangani. |
| Perlu data tambahan | Pendaftar perlu melengkapi data atau dokumen. |
| Selesai | Proses bantuan visa telah selesai. |

## 🧱 Struktur Basis Data (ERD)

Struktur basis data mengikuti ERD dalam dokumen perancangan proyek.

Entitas utama yang disebutkan dalam perancangan meliputi:

- `USERS` — data akun dan peran pengguna.
- `PENDAFTARAN` — data utama pendaftaran dan statusnya.
- `PENGAJUAN_VISA` — data pengajuan bantuan visa.
- `DOKUMEN_PERSYARATAN` — data persyaratan dokumen untuk program pendaftaran.

### Kategori Program

- **Jalur:** asing dan WNI.
- **Kategori program:** degree dan non-degree.
- **Jenjang degree:** S1, S2, dan S3.
- **Jenis program non-degree:** summer course, short course, student exchange, dan credit earning.
- **Pilihan visa:** urus sendiri dan dibantu.
- **Tujuan proses:** International Office (IO) dan UPT Pendidikan Luar Kampus (PDK).

## 📐 Dokumentasi Perancangan

Dokumentasi perancangan sistem meliputi:

- Use Case Diagram dan Deskripsi Aktor.
- Deskripsi Use Case.
- Scenario Diagram untuk 11 use case.
- Activity Diagram untuk 11 use case.
- Sequence Diagram untuk 11 use case.
- Entity Relationship Diagram (ERD).
- Keterangan nilai kolom atau enum pada ERD.
- Desain website.

### Daftar Use Case

1. Login.
2. Registrasi Akun.
3. Mengisi Registrasi Pendaftaran.
4. Mengajukan Bantuan Visa.
5. Mengunggah Dokumen Pendukung.
6. Memantau Status Pendaftaran.
7. Mengelola Dokumen Persyaratan.
8. Memverifikasi Pendaftaran.
9. Memproses Bantuan Visa.
10. Menyeleksi Pendaftar WNI.
11. Menerbitkan Registration Number.

## 🛠️ Teknologi

- **Backend:** PHP dengan framework Yii2.
- **Frontend:** HTML, CSS, JavaScript, dan template tampilan aplikasi.
- **Database:** Mengikuti konfigurasi proyek.
- **Version Control:** Git dan GitHub.

## 🚀 Instalasi

### Persyaratan Sistem

- PHP dengan versi yang sesuai.
- Composer.
- Database sesuai konfigurasi aplikasi.
- Web server atau lingkungan pengembangan lokal.

### Langkah Instalasi

1. Clone repository:

   ```bash
   git clone https://github.com/wlnsuci788/registrasi-mahasiswa.git
   ```

2. Masuk ke folder proyek:

   ```bash
   cd registrasi-mahasiswa
   ```

3. Instal dependensi:

   ```bash
   composer install
   ```

4. Sesuaikan konfigurasi database dan aplikasi dengan lingkungan lokal.
5. Jalankan aplikasi sesuai struktur dan konfigurasi Yii2 yang digunakan.

## 📁 Repository

[GitHub – registrasi-mahasiswa](https://github.com/wlnsuci788/registrasi-mahasiswa)

## 👤 Pengembang

Dwi Wulan Suci | 2311081012 | Teknologi Rekayasa Perangkat Lunak | Politeknik Negeri Padang

## 📌 Status Proyek

Dalam tahap pengembangan berdasarkan perancangan Portal Registrasi Mahasiswa Asing – Universitas Andalas.
