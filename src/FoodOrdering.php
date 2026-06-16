<?php
namespace App;
class FoodOrdering
{
# 1. validateUserRegistration
    public function validateUserRegistration(
        string $firstName,
        string $lastName,
        string $email,
        string $contact,
        string $password
    ): bool {
        if (
            empty($firstName) ||
            empty($lastName)  ||
            empty($email)     ||
            empty($contact)   ||
            empty($password)
        ) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

# 2. validateUserLogin
    public function validateUserLogin(string $email, string $password): bool
    {
        if (empty($email) || empty($password)) {
            return false;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        return true;
    }

# 3. calculateCartTotal
    public function calculateCartTotal(array $cartItems): float
    {
        $total = 0.0;

        foreach ($cartItems as $item) {
            $price    = isset($item['price'])    ? (float) $item['price']    : 0.0;
            $quantity = isset($item['quantity']) ? (int)   $item['quantity'] : 0;
            $total   += $price * $quantity;
        }

        return $total;
    }

# 4. isItemAlreadyInCart
    public function isItemAlreadyInCart(array $cartItems, string $itemName): bool
    {
        foreach ($cartItems as $item) {
            if (isset($item['itemName']) && $item['itemName'] === $itemName) {
                return true;
            }
        }

        return false;
    }

# 5. validateOrderData
    public function validateOrderData(array $orderData): bool
    {
        $required = ['firstName', 'lastName', 'email', 'address', 'contact', 'payment_mode'];

        foreach ($required as $field) {
            if (empty($orderData[$field])) {
                return false;
            }
        }

        if ($orderData['payment_mode'] === 'card') {
            return false;
        }

        return true;
    }

# 6. calculateOrderTotal
    public function calculateOrderTotal(float $subtotal, float $deliveryFee = 0.0): float
    {
        if ($subtotal < 0 || $deliveryFee < 0) {
            return 0.0;
        }

        return round($subtotal + $deliveryFee, 2);
    }

# 7. validateCancelOrder
    public function validateCancelOrder(int $orderId, string $reason): bool
    {
        if ($orderId <= 0) {
            return false;
        }

        if (empty(trim($reason))) {
            return false;
        }

        return true;
    }

# 8. validateReview
    public function validateReview(int $orderId, int $rating, string $reviewText): bool
    {
        if ($orderId <= 0) {
            return false;
        }

        if ($rating < 1 || $rating > 5) {
            return false;
        }

        if (empty(trim($reviewText))) {
            return false;
        }

        return true;
    }

# 9. validateReservation
    public function validateReservation(
        string $name,
        string $contact,
        int    $noOfGuests,
        string $reservedDate,
        string $reservedTime
    ): bool {
        if (empty($name) || empty($contact)) {
            return false;
        }

        if ($noOfGuests < 1) {
            return false;
        }

        $today = date('Y-m-d');
        if ($reservedDate < $today) {
            return false;
        }

        if (empty($reservedTime)) {
            return false;
        }

        return true;
    }

# 10. applyCartItemDiscount
    public function applyCartItemDiscount(float $price, float $discountPercent): float
    {
        if ($discountPercent < 0 || $discountPercent > 100) {
            return $price;
        }

        $discountAmount = $price * ($discountPercent / 100);

        return round($price - $discountAmount, 2);
    }
}
