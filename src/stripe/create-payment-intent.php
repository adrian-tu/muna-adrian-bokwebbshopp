<?php

require "stripe.php";

// We will respond using json to the client (browser)
header('Content-Type: application/json');

try {

    $intent = \Stripe\PaymentIntent::create([
        'amount' => 15000,
        'currency' => 'sek',
        'description' => 'En man som heter Ove',
        'automatic_payment_methods' => [ 'enabled' => true ]
    ]);
    echo json_encode([
        'clientSecret' => $intent->client_secret
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}