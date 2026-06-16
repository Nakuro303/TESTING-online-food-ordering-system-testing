BAHASA INDONESIA (ID):

!!!⚠️ PERINGATAN - PROYEK INI SEMATA-MATA DITUJUKAN UNTUK KEPERLUAN AKADEMIK DAN TUGAS PERKULIAHAN. WEB "online-food-ordering-system by asnaassalam" PADA PROYEK INI HANYA DIGUNAKAN SEBAGAI CONTOH UNTUK TESTING. ⚠️!!!

Sebuah aplikasi berbasis web yang dirancang untuk menyederhanakan proses pemesanan makanan secara online. Proyek ini dilengkapi dengan modul pengujian otomatis menggunakan PHPUnit guna memastikan keandalan fungsi sistem. Ikuti panduan lengkap ini untuk menyiapkan lingkungan pengembangan, menginstal dependensi, hingga menjalankan pengujian (testing) di perangkat lokal Anda.

Akses Akun:
1. Akun Admin:
   - email: admin@gmail.com
   - kata sandi : admin2024
2. Akun Pengguna:
   - email: jhon@gmail.com
   - kata sandi : JhonP
3. atau buat sendiri akun anda, formatnya:
   - email: user@gmail.com
   - Pkata sandi: userpassword

Persyaratan Sistem:
- PHP: Versi 8.0 atau yang lebih tinggi
- Database: MySQL atau MariaDB
- Web Server: Apache / Nginx (Sangat direkomendasikan menggunakan XAMPP atau Laragon)
- Version Control: Git

Langkah untuk menjalankan web online-food-ordering-system-testing
1. Instalisasi:
   ```bash
   git clone https://github.com/Nakuro303/TESTING-online-food-ordering-system-testing.git
   cd TESTING-online-food-ordering-system-testing
   ```
     
2. Instalasi Dependensi:
   ```bash
   composer install
   ```
   - Buka MySQL client (phpMyAdmin / HeidiSQL / terminal) dan impor file `restaurant.sql`
     
3. Menjalankan Unit Testing:
   - instalasi depedensi:
     ```bash
     composer require --dev phpunit/phpunit
     ```
   - menjalankan test:
     ```bash
     vendor/bin/phpunit tests/FoodOrderingTest.php
     ```
    
4. Menjalankan API Testing:
   - Install aplikasi Postman, melalui link berikut ini: https://www.postman.com/downloads/
   - Impor file `Food Ordering API Testing.postman_collection.json` kedalam aplikasi postman.
   - jalankan "Run Collection" untuk menjalankan test.
     
5. Menjalankan Database Testing:
   - Buka MySQL client (phpMyAdmin / HeidiSQL / terminal) dan impor file `database_testing.sql` pada folder `tests`
   - jalankan perintah, untuk menjalankan Database Testing:
     ```bash
     vendor/bin/phpunit tests/DatabaseTest.php
     ```
    
6. Menjalankan Selenium:
   ```bash
   cd tests
   python -m pytest test_food_ordering_selenium.py -v
   ```

ENGLISH (ENG):

!!!⚠️ WARNING - THIS PROJECT IS SOLELY INTENDED FOR ACADEMIC PURPOSES AND COLLEGE ASSIGNMENTS. THE "online-food-ordering-system by asnaassalam" WEB APPLICATION IN THIS PROJECT IS ONLY USED AS AN EXAMPLE FOR TESTING. ⚠️!!!

A web-based application designed to simplify the process of ordering food online. This project is equipped with an automated testing module using PHPUnit to ensure the reliability of the system's functions. Follow this comprehensive guide to set up your development environment, install dependencies, and execute the tests on your local machine.

Account Access:
1. Admin Account:
   - email: admin@gmail.com
   - password: admin2024
2. User Account:
   - email: jhon@gmail.com
   - password: JhonP
3. or create your own account, with the format:
   - email: user@gmail.com
   - password: userpassword

System Requirements:
- PHP: Version 8.0 or higher
- Database: MySQL or MariaDB
- Web Server: Apache / Nginx (XAMPP or Laragon is highly recommended)
- Version Control: Git

Steps to run the online-food-ordering-system-testing web application:
1. Installation:
   ```bash
   git clone https://github.com/Nakuro303/TESTING-online-food-ordering-system-testing.git
   cd TESTING-online-food-ordering-system-testing
   ```

2. Dependency Installation:
   ```bash
   composer install
   ```
   - Open your MySQL client (phpMyAdmin / HeidiSQL / terminal) and import the `restaurant.sql` file

3. Running Unit Testing:
   - dependency installation:
     ```bash
     composer require --dev phpunit/phpunit
     ```
   - running the test:
     ```bash
     vendor/bin/phpunit tests/FoodOrderingTest.php
     ```

4. Running API Testing:
   - Install the Postman application through the following link: https://www.postman.com/downloads/
   - Import the `Food Ordering API Testing.postman_collection.json` file into the Postman application.
   - Execute "Run Collection" to run the tests.

5. Running Database Testing:
   - Open your MySQL client (phpMyAdmin / HeidiSQL / terminal) and import the `database_testing.sql` file located inside the `tests` folder.
   - Run the following command to execute the Database Testing:
     ```bash
     vendor/bin/phpunit tests/DatabaseTest.php
     ```

6. Running Selenium:
   ```bash
   cd tests
   python -m pytest test_food_ordering_selenium.py -v
   ```

Tangkapan Layar - Screenshot:
1. Hasil Unit Testing - Unit Testing Results:
<img width="950" height="279" alt="Code_XTBxGPkd0E" src="https://github.com/user-attachments/assets/8bf0a8e4-44fb-4ac8-be65-e275e89995dd" />

2. Hasil API Testing (Postman) - API Testing (Postman) Results:
<img width="1920" height="1080" alt="Postman_pUlLmj9aA9" src="https://github.com/user-attachments/assets/1f688a59-a52c-4e2a-a650-a867bdda0341" />
<img width="1920" height="1080" alt="Postman_GDkoom8YaT" src="https://github.com/user-attachments/assets/702d1365-d940-4e63-8810-aab34acf0067" />
<img width="1920" height="1080" alt="Postman_tsaVzuMN9e" src="https://github.com/user-attachments/assets/229d7f7d-1aa2-4761-ab9d-76f486e43593" />

3. Hasil Database Testing - Database Testing Results:
<img width="889" height="279" alt="Code_4sK7cxWusx" src="https://github.com/user-attachments/assets/9c4a8b4c-e201-46f6-ad79-fd5fa6b7fb07" />

4. Hasil Selenium - Selenium Results:
<img width="1152" height="336" alt="Code_zHkUiE7Ede" src="https://github.com/user-attachments/assets/2acd351e-d48e-4c31-9e27-87a79ada4b45" />
