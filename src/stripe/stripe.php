<?php

// Import PHP-SDK
require "stripe-php/init.php";

// Update this to the key from YOUR Stripe account
const STRIPE_PUBLIC_KEY = 'sk_test_51QppmkQkMTLLdIB205koW4BokFMLai75gzXs2dmKb8NM1jkGg3dOiB2vfYlLw45EbtOR4IqgYyHhUOfoFheZWaPw00osFqFbK1';

// Configure PHP-SDK with our private key
\Stripe\Stripe::setApiKey(STRIPE_PUBLIC_KEY);