- [x] Ubah `backend/middleware/auth.php` untuk menambahkan debug message saat redirect (agar penyebab dashboard tidak tampil terlihat)
- [x] Ubah `public/rw/dashboard.php` untuk menampilkan pesan error dari querystring bila ada

- [ ] Ubah endpoint `backend/api/login.php` agar role `rw` vs `rt` benar (cek kemungkinan mismatch role)
- [ ] Jalankan container / uji akses `http://localhost:8080/rw/dashboard.php` tanpa login dan setelah login role rw
- [ ] Pastikan fetch `/backend/api/dashboard.php` tetap berjalan
