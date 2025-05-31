# Nama : Tasya Zahrani
# NPM : 2308107010006


## 📝 Deskripsi Singkat Aplikasi
Repositori ini merupakan proyek web aplikasi manajemen tugas (task management) yang dikembangkan menggunakan framework Laravel (PHP) di sisi backend dan kemungkinan menggunakan Vite serta Bootstrap di sisi frontend. Tujuan utama aplikasi ini adalah untuk membantu pengguna dalam mengelola daftar tugas (to-do list) secara efisien.

## Struktur direktori yang terdapat dalam repositori ini mencakup:
1. app/: Berisi logika aplikasi seperti controller dan model.
2. routes/: Menyimpan definisi rute aplikasi, kemungkinan besar dalam file web.php.
3. resources/: Tempat penyimpanan view (antarmuka pengguna) dan aset frontend lainnya.
4. public/: Direktori publik untuk aset yang dapat diakses oleh pengguna, seperti gambar dan file JavaScript/CSS.
5. database/: Berisi migrasi dan seeder untuk struktur basis data.

## File konfigurasi penting lainnya termasuk:
1. .env.example: Contoh file konfigurasi lingkungan.
2. composer.json: Mendefinisikan dependensi PHP yang digunakan dalam proyek.
3. package.json: Mendefinisikan dependensi JavaScript, menunjukkan penggunaan Vite untuk proses build frontend.

## 🖥️ Penjelasan Kode dan Antarmuka Pengguna
Meskipun file README.md dalam repositori ini tidak memberikan penjelasan detail mengenai fungsionalitas aplikasi, struktur direktori dan file yang ada menunjukkan bahwa aplikasi ini dirancang sebagai sistem manajemen tugas dengan fitur-fitur seperti:
- Menambahkan Tugas: Pengguna dapat menambahkan tugas baru ke dalam daftar.
- Melihat Daftar Tugas: Menampilkan semua tugas yang telah ditambahkan oleh pengguna.
- Mengedit Tugas: Memungkinkan pengguna untuk mengubah detail tugas yang sudah ada.
- Menghapus Tugas: Memberikan opsi untuk menghapus tugas yang tidak diperlukan lagi.
- Menandai Tugas Selesai: Fitur untuk menandai tugas yang telah diselesaikan.

Antarmuka pengguna kemungkinan besar dibangun menggunakan Blade (templating engine Laravel) dan Bootstrap untuk styling, dengan bantuan Vite untuk proses build dan pengelolaan aset frontend. Hal ini memungkinkan pembuatan antarmuka yang responsif dan interaktif.

## ⚙️ Cara Menjalankan Aplikasi Secara Lokal
Untuk menjalankan aplikasi ini di lingkungan lokal, langkah-langkah umum yang dapat diikuti adalah:
1. Kloning Repositori:
``` 
git clone https://github.com/tasyazahrani/taskFlow.git
cd taskFlow
```

2. Instalasi Dependensi Backend:
```
composer install
```

3. Instalasi Dependensi Frontend:
```
npm install
```

4. Menyalin dan Mengkonfigurasi File Lingkungan:
```
cp .env.example .env
php artisan key:generate
```

Kemudian, sesuaikan konfigurasi database dan lainnya dalam file .env.

5. Menjalankan Migrasi Basis Data:
```
php artisan migrate
```

6. Menjalankan Server Pengembangan:
```
php artisan serve
```

6. Menjalankan Build Frontend:
```
npm run dev
``` 
Setelah langkah-langkah di atas, aplikasi akan dapat diakses melalui [http://127.0.0.1:8000] di browser Anda.

## 📌 Catatan Tambahan
Repositori ini tampaknya merupakan proyek pribadi atau tugas akademik, mengingat informasi dalam README.md hanya mencantumkan nama dan NPM pengembang tanpa deskripsi tambahan. Untuk informasi lebih lanjut atau dokumentasi tambahan, Anda dapat menghubungi pemilik repositori atau memeriksa kode sumber secara langsung.

