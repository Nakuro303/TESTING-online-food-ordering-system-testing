<?php
use PHPUnit\Framework\TestCase;
use App\FoodOrdering;
require_once __DIR__ . '/../src/FoodOrdering.php';

class FoodOrderingTest extends TestCase
{
    private FoodOrdering $fo;

    protected function setUp(): void
    {
        $this->fo = new FoodOrdering();
    }

# FUNCTION 1
    public function testValidateUserRegistration(): void
    {
    # ASSERTION 1
        $this->assertTrue(
            $this->fo->validateUserRegistration(
                'Budi', 'Santoso', 'budi@gmail.com', '08123456789', 'Pass@123'
            ),
            'Registrasi dengan data valid seharusnya berhasil.'
        );

    # ASSERTION 2
        $this->assertFalse(
            $this->fo->validateUserRegistration(
                'Budi', 'Santoso', 'budi-invalid-email', '08123456789', 'Pass@123'
            ),
            'Email tidak valid seharusnya ditolak.'
        );

    # ASSERTION 3
        $this->assertFalse(
            $this->fo->validateUserRegistration(
                'Budi', 'Santoso', 'budi@gmail.com', '08123456789', ''
            ),
            'Password kosong seharusnya ditolak.'
        );
    }

# FUNCTION 2
    public function testValidateUserLogin(): void
    {
    # ASSERTION 4
        $this->assertTrue(
            $this->fo->validateUserLogin('admin@gmail.com', 'admin2024'),
            'Login dengan email dan password valid seharusnya berhasil.'
        );

    # ASSERTION 5
        $this->assertFalse(
            $this->fo->validateUserLogin('', 'admin2024'),
            'Email kosong seharusnya ditolak pada login.'
        );

    # ASSERTION 6
        $this->assertFalse(
            $this->fo->validateUserLogin('bukan-email', 'password123'),
            'Email berformat salah seharusnya ditolak.'
        );
    }

# FUNCTION 3
    public function testCalculateCartTotal(): void
    {
        $cartItems = [
            ['itemName' => 'BBQ Chicken Pizza', 'price' => 1000.0, 'quantity' => 2],
            ['itemName' => 'French Fries',       'price' => 760.0,  'quantity' => 1],
        ];

    # ASSSERTION 7
        $this->assertEquals(
            2760.0,
            $this->fo->calculateCartTotal($cartItems),
            'Total keranjang tidak sesuai dengan kalkulasi yang diharapkan.'
        );

    # ASSERTION 8
        $this->assertEquals(
            0.0,
            $this->fo->calculateCartTotal([]),
            'Keranjang kosong seharusnya menghasilkan total 0.'
        );

    # ASSERTION 9
        $singleItem = [['itemName' => 'Veggie Supreme', 'price' => 800.0, 'quantity' => 3]];
        $this->assertEquals(
            2400.0,
            $this->fo->calculateCartTotal($singleItem),
            'Total satu item dengan quantity 3 seharusnya 2400.'
        );
    }

# FUNCTION 4
    public function testIsItemAlreadyInCart(): void
    {
        $cart = [
            ['itemName' => 'BBQ Chicken Pizza', 'price' => 1000.0, 'quantity' => 1],
            ['itemName' => 'French Fries',       'price' => 760.0,  'quantity' => 1],
        ];

    # ASSERTION 10
        $this->assertTrue(
            $this->fo->isItemAlreadyInCart($cart, 'BBQ Chicken Pizza'),
            'Item yang sudah ada di keranjang seharusnya terdeteksi.'
        );

    # ASSERTION 11
        $this->assertFalse(
            $this->fo->isItemAlreadyInCart($cart, 'Prawn Pizza'),
            'Item yang belum ada di keranjang seharusnya tidak terdeteksi.'
        );

    # ASSERTION 12
        $this->assertFalse(
            $this->fo->isItemAlreadyInCart([], 'BBQ Chicken Pizza'),
            'Pengecekan pada keranjang kosong seharusnya mengembalikan false.'
        );
    }

# FUNCTION 5
    public function testValidateOrderData(): void
    {
        $validOrder = [
            'firstName'    => 'Budi',
            'lastName'     => 'Santoso',
            'email'        => 'budi@gmail.com',
            'address'      => 'Jl. Merdeka No. 17, Jakarta',
            'contact'      => '08123456789',
            'payment_mode' => 'cash',
        ];

    # ASSERTION 13
        $this->assertTrue(
            $this->fo->validateOrderData($validOrder),
            'Data pesanan lengkap dengan metode cash seharusnya valid.'
        );

    # ASSERTION 14
        $cardOrder               = $validOrder;
        $cardOrder['payment_mode'] = 'card';
        $this->assertFalse(
            $this->fo->validateOrderData($cardOrder),
            'Metode pembayaran "card" seharusnya ditolak.'
        );

    # ASSERTION 15
        $noAddress            = $validOrder;
        $noAddress['address'] = '';
        $this->assertFalse(
            $this->fo->validateOrderData($noAddress),
            'Pesanan tanpa alamat seharusnya ditolak.'
        );
    }

# FUNCTION 6
    public function testCalculateOrderTotal(): void
    {
    # ASSERTION 16
        $this->assertEquals(
            2775.0,
            $this->fo->calculateOrderTotal(2760.0, 15.0),
            'Grand total dengan ongkos kirim seharusnya 2775.'
        );

    # ASSERTION 17
        $this->assertEquals(
            1800.0,
            $this->fo->calculateOrderTotal(1800.0, 0.0),
            'Grand total tanpa ongkos kirim seharusnya sama dengan subtotal.'
        );

    # ASSERTION 18
        $this->assertEquals(
            0.0,
            $this->fo->calculateOrderTotal(-500.0, 10.0),
            'Subtotal negatif seharusnya mengembalikan 0.'
        );
    }

# FUNCTION 7
    public function testValidateCancelOrder(): void
    {
    # ASSERTION 19
        $this->assertTrue(
            $this->fo->validateCancelOrder(5, 'Saya ingin mengubah pesanan saya'),
            'Pembatalan dengan ID valid dan alasan terisi seharusnya berhasil.'
        );

    # ASSERTION 20
        $this->assertFalse(
            $this->fo->validateCancelOrder(0, 'Alasan valid'),
            'Order ID 0 seharusnya ditolak saat pembatalan.'
        );

    # ASSERTION 21
        $this->assertFalse(
            $this->fo->validateCancelOrder(3, '   '),
            'Alasan kosong (spasi) seharusnya ditolak saat pembatalan.'
        );
    }

# FUNCTION 8
    public function testValidateReview(): void
    {
    # ASSERTION 22
        $this->assertTrue(
            $this->fo->validateReview(10, 5, 'Makanan enak dan cepat!'),
            'Ulasan dengan data valid seharusnya diterima.'
        );

    # ASSERTION 23
        $this->assertFalse(
            $this->fo->validateReview(10, 6, 'Ulasan valid'),
            'Rating lebih dari 5 seharusnya ditolak.'
        );

    # ASSERTION 24
        $this->assertFalse(
            $this->fo->validateReview(10, 4, ''),
            'Teks ulasan kosong seharusnya ditolak.'
        );
    }

# FUNCTION 9
    public function testValidateReservation(): void
    {
        $futureDate = date('Y-m-d', strtotime('+7 days'));
        $pastDate   = date('Y-m-d', strtotime('-1 day'));

    # ASSERTION 25
        $this->assertTrue(
            $this->fo->validateReservation('Budi Santoso', '08123456789', 4, $futureDate, '19:00'),
            'Reservasi dengan data valid di masa depan seharusnya diterima.'
        );

    # ASSERTION 26
        $this->assertFalse(
            $this->fo->validateReservation('Budi Santoso', '08123456789', 2, $pastDate, '19:00'),
            'Reservasi dengan tanggal lampau seharusnya ditolak.'
        );

    # ASSERTION 27
        $this->assertFalse(
            $this->fo->validateReservation('Budi Santoso', '08123456789', 0, $futureDate, '19:00'),
            'Reservasi dengan jumlah tamu 0 seharusnya ditolak.'
        );
    }

# FUNCTION 10
    public function testApplyCartItemDiscount(): void
    {
    # ASSERTION 28
        $this->assertEquals(
            900.0,
            $this->fo->applyCartItemDiscount(1000.0, 10.0),
            'Harga setelah diskon 10% seharusnya 900.'
        );

    # ASSERTION 29
        $this->assertEquals(
            1200.0,
            $this->fo->applyCartItemDiscount(1200.0, 0.0),
            'Diskon 0% seharusnya tidak mengubah harga.'
        );

    # ASSERTION 30
        $this->assertEquals(
            800.0,
            $this->fo->applyCartItemDiscount(800.0, -20.0),
            'Diskon negatif tidak valid; harga seharusnya tetap 800.'
        );
    }
}
