<?php

Flight::route('POST /checkout', function() {
    $currentUser = Flight::get('user');

    if (!$currentUser) {
        Flight::json(['success' => false, 'message' => 'User not authenticated'], 401);
        return;
    }

    try {
        $data = Flight::request()->data->getData();

        // Validate required fields
        $required = ['email', 'firstName', 'lastName', 'phone', 'address', 'city', 'zip', 'country', 'paymentMethod'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                Flight::json(['success' => false, 'message' => "Missing required field: $field"], 400);
                return;
            }
        }

        // Verify email matches logged-in user
        if ($data['email'] !== $currentUser->email) {
            Flight::json(['success' => false, 'message' => 'Email must match your registered account email'], 400);
            return;
        }

        // Get cart items
        $cartItems = Flight::cartItemService()->get_cart_by_user($currentUser->user_id);
        if (empty($cartItems)) {
            Flight::json(['success' => false, 'message' => 'Cart is empty'], 400);
            return;
        }

        // Calculate total
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += floatval($item['price']) * intval($item['quantity']);
        }
        $shipping = 5.00;
        $total = $subtotal + $shipping;

        // 1. Create or update address
        $addressId = Flight::addressService()->add([
            'user_id' => $currentUser->user_id,
            'full_name' => trim($data['firstName'] . ' ' . $data['lastName']),
            'street' => $data['address'],
            'city' => $data['city'],
            'zip_code' => $data['zip'],
            'country' => $data['country'],
            'phone' => $data['phone']
        ]);

        // 2. Create order
        $orderId = Flight::orderService()->add([
            'user_id' => $currentUser->user_id,
            'total_amount' => $total,
            'status' => 'pending',
            'shipping_address_id' => $addressId
        ]);

        // 3. Create order items from cart
        foreach ($cartItems as $item) {
            Flight::orderItemService()->add([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        // 4. Create payment record
        $paymentId = Flight::paymentService()->add([
            'order_id' => $orderId,
            'amount' => $total,
            'method' => $data['paymentMethod'],
            'status' => 'completed'
        ]);

        // 5. Send confirmation email
        $emailSent = sendOrderConfirmationEmail(
            $data['email'],
            $data['firstName'] . ' ' . $data['lastName'],
            $orderId,
            $total,
            $cartItems
        );

        // 6. Clear cart
        Flight::cartItemService()->clear_cart($currentUser->user_id);

        Flight::json([
            'success' => true,
            'data' => [
                'order_id' => $orderId,
                'payment_id' => $paymentId,
                'address_id' => $addressId,
                'total' => $total,
                'email_sent' => $emailSent
            ],
            'message' => 'Order placed successfully!'
        ]);

    } catch (Exception $e) {
        error_log("Checkout error: " . $e->getMessage());
        Flight::json([
            'success' => false,
            'message' => 'Checkout failed: ' . $e->getMessage()
        ], 500);
    }
});

/**
 * Send order confirmation email
 */
function sendOrderConfirmationEmail($toEmail, $customerName, $orderId, $total, $cartItems) {
    $subject = "Order Confirmation - Fishing Store #" . $orderId;

    // Build email body
    $itemsList = "";
    foreach ($cartItems as $item) {
        $itemTotal = floatval($item['price']) * intval($item['quantity']);
        $itemsList .= "- {$item['name']} x {$item['quantity']} = €" . number_format($itemTotal, 2) . "\n";
    }

    $message = "Dear {$customerName},\n\n";
    $message .= "Thank you for your purchase!\n\n";
    $message .= "Your order has been successfully placed and is being processed.\n\n";
    $message .= "Order Details:\n";
    $message .= "Order ID: #{$orderId}\n";
    $message .= "Total Amount: €" . number_format($total, 2) . "\n\n";
    $message .= "Items Ordered:\n";
    $message .= $itemsList;
    $message .= "\nYour package will be shipped soon!\n\n";
    $message .= "Thank you for shopping with Fishing Store.\n\n";
    $message .= "Best regards,\n";
    $message .= "The Fishing Store Team";

    // Email headers
    $headers = "From: noreply@fishingstore.com\r\n";
    $headers .= "Reply-To: support@fishingstore.com\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send email
    $emailSent = mail($toEmail, $subject, $message, $headers);

    // Log email attempt
    if ($emailSent) {
        error_log("Order confirmation email sent to: $toEmail for order #$orderId");
    } else {
        error_log("Failed to send order confirmation email to: $toEmail for order #$orderId");
    }

    return $emailSent;
}
?>
