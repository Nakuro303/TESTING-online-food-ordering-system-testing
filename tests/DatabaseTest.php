<?php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test; // Impor atribut Test modern untuk PHPUnit 11

class DatabaseTest extends TestCase
{
    private PDO $db;
    protected function setUp(): void
    {
        $host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? '127.0.0.1';
        $name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'restaurant_testing';
        $user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
        $pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? '';
        $port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4";

        $this->db = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);

        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

// DB SCRIPT 1
    #[Test]
    public function testInsertUserBaru(): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (email, firstName, lastName, contact, password)
             VALUES (:email, :firstName, :lastName, :contact, :password)'
        );
        $stmt->execute([
            ':email'     => 'testuser@gmail.com',
            ':firstName' => 'Test',
            ':lastName'  => 'User',
            ':contact'   => '08111111111',
            ':password'  => password_hash('Secret@123', PASSWORD_BCRYPT),
        ]);

    // ASSERTION 1
        $this->assertEquals(1, $stmt->rowCount(),
            'INSERT user baru seharusnya menghasilkan 1 baris terpengaruh.');

    // ASSERTION 2
        $row = $this->db->query(
            "SELECT * FROM users WHERE email = 'testuser@gmail.com'"
        )->fetch();
        $this->assertEquals('Test', $row['firstName'],
            'firstName yang tersimpan harus sesuai dengan yang diinsert.');
    }

//  DB SCRIPT 2
    #[Test]
    public function testSelectUserBerdasarkanEmail(): void
    {
        $this->db->prepare(
            'INSERT INTO users (email, firstName, lastName, contact, password)
             VALUES (:email, :firstName, :lastName, :contact, :password)'
        )->execute([
            ':email'     => 'budi@gmail.com',
            ':firstName' => 'Budi',
            ':lastName'  => 'Santoso',
            ':contact'   => '08122222222',
            ':password'  => password_hash('BudiPass1!', PASSWORD_BCRYPT),
        ]);
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email'
        );
        $stmt->execute([':email' => 'budi@gmail.com']);
        $user = $stmt->fetch();

    // ASSERTION 3
        $this->assertNotFalse($user,
            'User dengan email budi@gmail.com seharusnya ditemukan.');

    // ASSERTION 4
        $this->assertEquals('Santoso', $user['lastName'],
            'lastName user yang ditemukan harus Santoso.');
    }

// DB SCRIPT 3
    #[Test]
    public function testInsertMenuItemBaru(): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO menuitem (itemName, catName, price, status, description, image, updatedDate)
             VALUES (:itemName, :catName, :price, :status, :description, :image, NOW())'
        );
        $stmt->execute([
            ':itemName'    => 'Nasi Goreng Special',
            ':catName'     => 'Main Course',
            ':price'       => '45000',
            ':status'      => 'Available',
            ':description' => 'Nasi goreng dengan telur dan ayam.',
            ':image'       => 'nasi-goreng.jpg',
        ]);
        $itemId = (int) $this->db->lastInsertId();

    // ASSERTION 5
        $this->assertGreaterThan(0, $itemId,
            'lastInsertId seharusnya menghasilkan ID valid (> 0).');

    // ASSERTION 6
        $stmt2 = $this->db->prepare('SELECT * FROM menuitem WHERE itemId = :id');
        $stmt2->execute([':id' => $itemId]);
        $item = $stmt2->fetch();
        $this->assertEquals('Nasi Goreng Special', $item['itemName'],
            'itemName yang tersimpan harus sesuai dengan yang diinsert.');
    }

//  DB SCRIPT 4
    #[Test]
    public function testFilterMenuItemBerdasarkanStatus(): void
    {
        $insert = $this->db->prepare(
            'INSERT INTO menuitem (itemName, catName, price, status, description, image, updatedDate)
             VALUES (:itemName, :catName, :price, :status, :description, :image, NOW())'
        );
        $insert->execute([
            ':itemName'    => 'Pizza Tersedia',
            ':catName'     => 'Pizza',
            ':price'       => '80000',
            ':status'      => 'Available',
            ':description' => 'Pizza siap saji.',
            ':image'       => 'pizza.jpg',
        ]);
        $insert->execute([
            ':itemName'    => 'Burger Habis',
            ':catName'     => 'Burger',
            ':price'       => '55000',
            ':status'      => 'Unavailable',
            ':description' => 'Burger sedang tidak tersedia.',
            ':image'       => 'burger.jpg',
        ]);
        $stmt = $this->db->prepare(
            "SELECT * FROM menuitem WHERE status = 'Available'"
        );
        $stmt->execute();
        $availableItems = $stmt->fetchAll();

    // ASSERTION 7: Minimal 1 item berstatus Available
        $this->assertGreaterThanOrEqual(1, count($availableItems),
            'Harus ada minimal 1 item berstatus Available.');

    // ASSERTION 8: Semua item yang dikembalikan berstatus Available
        foreach ($availableItems as $item) {
            $this->assertEquals('Available', $item['status'],
                "Semua item yang diambil harus berstatus 'Available'.");
        }
    }

//  DB SCRIPT 5
    #[Test]
    public function testInsertDanBacaOrderBaru(): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO orders
                (email, firstName, lastName, phone, address, pmode, sub_total, grand_total)
             VALUES
                (:email, :firstName, :lastName, :phone, :address, :pmode, :sub_total, :grand_total)'
        );
        $stmt->execute([
            ':email'       => 'customer@gmail.com',
            ':firstName'   => 'Andi',
            ':lastName'    => 'Wijaya',
            ':phone'       => '08133333333',
            ':address'     => 'Jl. Sudirman No. 10, Jakarta',
            ':pmode'       => 'cash',
            ':sub_total'   => 100000.00,
            ':grand_total' => 115000.00,
        ]);
        $orderId = (int) $this->db->lastInsertId();

    // ASSERTION 9
        $this->assertGreaterThan(0, $orderId,
            'Order ID seharusnya lebih dari 0 setelah insert.');
        $stmt2 = $this->db->prepare('SELECT * FROM orders WHERE order_id = :id');
        $stmt2->execute([':id' => $orderId]);
        $order = $stmt2->fetch();

    // ASSERTION 10
        $this->assertEquals('115000.00', $order['grand_total'],
            'grand_total yang tersimpan harus sesuai dengan nilai yang diinput.');
    }

//  DB SCRIPT 6
    #[Test]
    public function testUpdateStatusOrder(): void
    {
        $this->db->prepare(
            'INSERT INTO orders
                (email, firstName, lastName, phone, address, pmode, sub_total, grand_total)
             VALUES
                (:email, :firstName, :lastName, :phone, :address, :pmode, :sub_total, :grand_total)'
        )->execute([
            ':email'       => 'pelanggan@gmail.com',
            ':firstName'   => 'Siti',
            ':lastName'    => 'Rahma',
            ':phone'       => '08144444444',
            ':address'     => 'Jl. Gatot Subroto No. 5',
            ':pmode'       => 'cash',
            ':sub_total'   => 80000.00,
            ':grand_total' => 80000.00,
        ]);
        $orderId = (int) $this->db->lastInsertId();
        $updateStmt = $this->db->prepare(
            'UPDATE orders SET order_status = :status WHERE order_id = :id'
        );
        $updateStmt->execute([
            ':status' => 'Order Confirmed',
            ':id'     => $orderId,
        ]);

    // ASSERTION 11
        $this->assertEquals(1, $updateStmt->rowCount(),
            'UPDATE order_status seharusnya mempengaruhi tepat 1 baris.');

    // ASSERTION 12
        $stmt = $this->db->prepare('SELECT order_status FROM orders WHERE order_id = :id');
        $stmt->execute([':id' => $orderId]);
        $row = $stmt->fetch();
        $this->assertEquals('Order Confirmed', $row['order_status'],
            'order_status setelah update harus menjadi Order Confirmed.');
    }

//  DB SCRIPT 7
    #[Test]
    public function testInsertDanHitungItemCart(): void
    {
        $email = 'cartuser@gmail.com';
        $insert = $this->db->prepare(
            'INSERT INTO cart (itemName, price, image, quantity, catName, email, total_price)
             VALUES (:itemName, :price, :image, :quantity, :catName, :email, :total_price)'
        );
        $insert->execute([
            ':itemName'    => 'BBQ Chicken Pizza',
            ':price'       => 1000,
            ':image'       => 'bbq-pizza.jpg',
            ':quantity'    => 2,
            ':catName'     => 'Pizza',
            ':email'       => $email,
            ':total_price' => '2000',
        ]);
        $insert->execute([
            ':itemName'    => 'French Fries',
            ':price'       => 760,
            ':image'       => 'fries.jpg',
            ':quantity'    => 1,
            ':catName'     => 'Appetizer',
            ':email'       => $email,
            ':total_price' => '760',
        ]);

        $countStmt = $this->db->prepare(
            'SELECT COUNT(*) AS jumlah FROM cart WHERE email = :email'
        );
        $countStmt->execute([':email' => $email]);
        $result = $countStmt->fetch();

    // ASSERTION 13
        $this->assertEquals(2, (int) $result['jumlah'],
            'Jumlah item di cart seharusnya 2 setelah dua kali insert.');

    // ASSERTION 14
        $this->assertNotEquals(0, (int) $result['jumlah'],
            'Cart tidak boleh kosong setelah insert.');
    }

//  DB SCRIPT 8
    #[Test]
    public function testInsertReservasiDanValidasiData(): void
    {
        $reservationId = 'RES-TEST-' . time();
        $futureDate    = date('Y-m-d', strtotime('+7 days'));
        $stmt = $this->db->prepare(
            'INSERT INTO reservations
                (reservation_id, email, name, contact, noOfGuests, reservedTime, reservedDate)
             VALUES
                (:reservation_id, :email, :name, :contact, :noOfGuests, :reservedTime, :reservedDate)'
        );
        $stmt->execute([
            ':reservation_id' => $reservationId,
            ':email'          => 'tamu@gmail.com',
            ':name'           => 'Deni Permana',
            ':contact'        => '08155555555',
            ':noOfGuests'     => 4,
            ':reservedTime'   => '19:00',
            ':reservedDate'   => $futureDate,
        ]);

        $fetch = $this->db->prepare(
            'SELECT * FROM reservations WHERE reservation_id = :id'
        );
        $fetch->execute([':id' => $reservationId]);
        $reservation = $fetch->fetch();

    // ASSERTION 15
        $this->assertNotFalse($reservation,
            'Reservasi yang baru diinsert seharusnya bisa ditemukan.');

    // ASSERTION 16
        $this->assertEquals(4, (int) $reservation['noOfGuests'],
            'Jumlah tamu yang tersimpan harus 4.');
    }

//  DB SCRIPT 9
    #[Test]
    public function testInsertReviewDanCekRating(): void
    {
        $this->db->prepare(
            'INSERT INTO orders
                (email, firstName, lastName, phone, address, pmode, sub_total, grand_total)
             VALUES
                (:email, :firstName, :lastName, :phone, :address, :pmode, :sub_total, :grand_total)'
        )->execute([
            ':email'       => 'reviewer@gmail.com',
            ':firstName'   => 'Rizki',
            ':lastName'    => 'Pratama',
            ':phone'       => '08166666666',
            ':address'     => 'Jl. Ahmad Yani No. 3',
            ':pmode'       => 'cash',
            ':sub_total'   => 50000.00,
            ':grand_total' => 50000.00,
        ]);
        $orderId = (int) $this->db->lastInsertId();
        $stmt = $this->db->prepare(
            'INSERT INTO reviews (email, order_id, rating, review_text)
             VALUES (:email, :order_id, :rating, :review_text)'
        );
        $stmt->execute([
            ':email'       => 'reviewer@gmail.com',
            ':order_id'    => $orderId,
            ':rating'      => 5,
            ':review_text' => 'Makanan sangat enak dan cepat!',
        ]);

        $reviewId = (int) $this->db->lastInsertId();
        $fetch = $this->db->prepare('SELECT * FROM reviews WHERE review_id = :id');
        $fetch->execute([':id' => $reviewId]);
        $review = $fetch->fetch();

    // ASSERTION 17
        $this->assertEquals(5, (int) $review['rating'],
            'Rating yang tersimpan seharusnya 5.');

    // ASSERTION 18
        $this->assertEquals('Pending', $review['status'],
            "Status review baru seharusnya 'Pending' secara default.");
    }

//  DB SCRIPT 10
    #[Test]
    public function testDeleteCartItemDanVerifikasi(): void
    {
        $email = 'deletetest@gmail.com';
        $this->db->prepare(
            'INSERT INTO cart (itemName, price, image, quantity, catName, email, total_price)
             VALUES (:itemName, :price, :image, :quantity, :catName, :email, :total_price)'
        )->execute([
            ':itemName'    => 'Strawberry Mocktail',
            ':price'       => 550,
            ':image'       => 'strawberry-drink.png',
            ':quantity'    => 1,
            ':catName'     => 'Beverage',
            ':email'       => $email,
            ':total_price' => '550',
        ]);
        $cartId = (int) $this->db->lastInsertId();
        $deleteStmt = $this->db->prepare('DELETE FROM cart WHERE id = :id');
        $deleteStmt->execute([':id' => $cartId]);

    // ASSERTION 19
        $this->assertEquals(1, $deleteStmt->rowCount(),
            'DELETE cart item seharusnya mempengaruhi tepat 1 baris.');

    // ASSERTION 20
        $checkStmt = $this->db->prepare('SELECT * FROM cart WHERE id = :id');
        $checkStmt->execute([':id' => $cartId]);
        $deleted = $checkStmt->fetch();
        $this->assertFalse($deleted,
            'Item yang sudah dihapus seharusnya tidak dapat ditemukan lagi.');
    }
}