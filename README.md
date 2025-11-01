# SmartERP - Sistem Manajemen Bisnis Kostum

Project personal untuk belajar Vue.js sambil bikin aplikasi ERP lengkap. Sistem ini dibuat untuk mengelola bisnis rental dan penjualan kostum - dari inventory, transaksi, employee management, sampe customer support.

## Tentang Project Ini

Jadi ceritanya ini project yang saya kerjain buat belajar Vue.js 3 dari nol. Daripada cuma ngikutin tutorial, saya coba bikin sesuatu yang lebih kompleks dan realistis. Akhirnya kepikiran bikin sistem ERP sederhana karena challengingnya pas - gak terlalu simpel tapi juga gak terlalu ribet buat dikerjain sendiri.

Dikerjain sekitar 3 bulanan sambil belajar Vue.js dan Tailwind CSS dari dokumentasi sama tutorial online. Total ada 9 halaman dengan berbagai fitur yang saya butuhkan buat manage bisnis kostum.

## Fitur-fitur yang Ada (9 Modul)

**1. Dashboard**
- Halaman utama buat lihat ringkasan bisnis
- Total pendapatan, jumlah transaksi, dan metrics penting lainnya
- Overview performa dalam satu tampilan

**2. Products (Kelola Produk)**
- Tambah, edit, hapus produk kostum
- Filter berdasarkan kategori
- Fitur search buat cari produk cepet
- Manage inventory kostum rental dan penjualan

**3. Transactions (Transaksi)**
- Form buat catat transaksi jual/sewa
- Input data pelanggan dan detail pesanan
- History transaksi

**4. Employees (Data Karyawan)**
- Database karyawan lengkap
- Info departemen dan posisi
- Management data pegawai

**5. Attendance (Absensi)**
- Catat kehadiran karyawan
- Jam masuk dan jam keluar
- Laporan absensi harian

**6. Feedback (Review Pelanggan)**
- Customer bisa kasih rating bintang
- Komentar dan review produk
- Management feedback dari pelanggan

**7. Tickets (Support/Komplain)**
- Kelola komplain pelanggan
- Tracking pertanyaan dan request
- System ticketing sederhana

**8. Login Page**
- Halaman untuk masuk ke sistem
- Form authentication

**9. Register Page**
- Halaman daftar akun baru
- Form registrasi user

## Tech Stack

Pakai Vue.js 3 (Options API) karena menurut saya lebih gampang dipahami buat yang baru belajar. Styling pakai Tailwind CSS - ini juga baru saya pelajari dan ternyata bikin responsive design jadi lebih cepet.

- Vue.js 3
- Tailwind CSS  
- JavaScript
- HTML5 & CSS3

## Cara Jalanin

Kalau mau coba jalanin di lokal:

```bash
# Clone dulu
git clone https://github.com/Erlina130104/proyek-portofolio-app.git
cd proyek-portofolio-app

# Install
npm install

# Run
npm run dev
```

Terus buka `http://localhost:5173` di browser.

## Notes

Beberapa hal yang saya pelajari dari project ini:

1. **Component Structure** - Awalnya agak bingung gimana cara organize component yang bener, tapi lama-lama mulai paham pola-polanya
2. **Reactive Data** - Konsep reactivity di Vue lumayan tricky di awal, tapi setelah praktek langsung jadi lebih ngerti
3. **Responsive Design** - Pake Tailwind bikin responsive jadi lebih straightforward dibanding CSS manual
4. **Code Documentation** - Saya biasain kasih komentar di kode supaya kalau nanti buka lagi gak lupa apa fungsinya

Kode saya comment lumayan banyak (sekitar 1.500an baris) dalam Bahasa Indonesia biar kalau ada yang mau baca lebih gampang ngertinya.

## Rencana Kedepan

Project ini masih frontend only. Kedepannya pengen coba:
- Connect ke backend (lagi belajar Node.js)
- Pakai database beneran (MySQL atau MongoDB)
- Bikin fitur login/register
- Export laporan jadi PDF
- Mungkin tambahin dark mode

Tapi itu nanti kalau udah lebih paham backend development.

## Contact

Kalau ada pertanyaan atau mau diskusi tentang project ini:

**Erlina Febiola Nainggolan**  
📧 erlinanainggolan130104@gmail.com  
📱 +62 8971798041

---

*Project ini saya buat untuk belajar dan portfolio. Feedback atau saran always welcome!*
