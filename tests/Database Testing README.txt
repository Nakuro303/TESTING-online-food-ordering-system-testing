DB SCRIPT 1 — Insert User Baru
DB SCRIPT 1: INSERT ke tabel users
ASSERTION 1: Baris berhasil dimasukkan
ASSERTION 2: Data dapat dibaca kembali dari database

DB SCRIPT 2 — Select User Berdasarkan Email
DB SCRIPT 2: SELECT user berdasarkan email
ASSERTION 3: User ditemukan (bukan false)
ASSERTION 4: lastName sesuai

DB SCRIPT 3 — Insert Menu Item Baru
DB SCRIPT 3: INSERT ke tabel menuitem
ASSERTION 5: ID yang dihasilkan lebih dari 0
ASSERTION 6: Item dapat ditemukan di database

DB SCRIPT 4 — Filter Menu Item Berdasarkan Status
DB SCRIPT 4: SELECT berdasarkan status 'Available'
ASSERTION 7: Minimal 1 item berstatus Available
ASSERTION 8: Semua item yang dikembalikan berstatus Available

DB SCRIPT 5 — Insert dan Baca Order Baru
DB SCRIPT 5: INSERT ke tabel orders
ASSERTION 9: Order ID valid
ASSERTION 10: grand_total sesuai

DB SCRIPT 6 — Update Status Order
DB SCRIPT 6: UPDATE order_status
ASSERTION 11: rowCount() harus 1 (satu baris terubah)
ASSERTION 12: Nilai status terbaru sesuai di database

DB SCRIPT 7 — Insert dan Hitung Item di Cart
DB SCRIPT 7: INSERT beberapa item ke cart
ASSERTION 13: Harus ada 2 item di cart
ASSERTION 14: Jumlah tidak boleh 0

DB SCRIPT 8 — Insert Reservasi dan Validasi Data
DB SCRIPT 8: INSERT ke tabel reservations
ASSERTION 15: Reservasi ditemukan
ASSERTION 16: noOfGuests sesuai

DB SCRIPT 9 — Insert Review dan Cek Rating
DB SCRIPT 9: INSERT ke tabel reviews
ASSERTION 17: Rating yang tersimpan harus 5
ASSERTION 18: Status default adalah Pending

DB SCRIPT 10 — Delete Cart Item dan Verifikasi
DB SCRIPT 10: DELETE item dari cart
ASSERTION 19: rowCount() harus 1 setelah delete
ASSERTION 20: Item sudah tidak ada di database


# Jalankan HANYA database testing (tidak ganggu test lain)
vendor/bin/phpunit tests/DatabaseTest.php

# Jalankan dengan output detail per assertion
vendor/bin/phpunit tests/DatabaseTest.php --testdox

# Jalankan semua test (unit + database)
vendor/bin/phpunit

# Jalankan hanya unit testing (FoodOrderingTest)
vendor/bin/phpunit tests/FoodOrderingTest.php