<?php
$book = [
    'title' => 'En man som heter Ove',
    'author' => 'Fredrik Backman',
    'price' => 150, // Pris i SEK
    'description' => 'Boken handlar om Ove, en man på 59 år som kör en Saab',
    'image' => 'istockphoto-96379220-612x612.jpg' // Använd relativ sökväg här
];

// Om betalning redan har genomförts
$paymentIntentId = $_GET['payment_intent'] ?? null;

if($paymentIntentId){
    require "stripe.php";
    $intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
    if($intent->status === 'succeeded') {
        header('Location: success.php?'.
            'status='.urlencode($intent->status).
            'payment_intent='.urlencode($paymentIntentId)    
        );
    } else {
        header('Location: cancel.php');
    }
}
?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?></title>
    <link rel="stylesheet" href="../css/style.css">

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        // Uppdatera denna nyckel till nyckeln från ditt Stripe-konto
        const STRIPE_PUBLIC_KEY = 'pk_test_51QppmkQkMTLLdIB2QrW7vkF3HUXaK0CJB859E14937p2dC6XUEHz2Yq8OT7RMxhOpnUyder27LX0GNSrF6axKJwh00hvpplpLd';
    </script>
</head>
<body>
    <div class="book-details">
        <img src="../images/<?php echo $book['image']; ?>" alt="Bokomslag">
        <h1><?php echo $book['title']; ?></h1>
        <h2>Av: <?php echo $book['author']; ?></h2>
        <p><?php echo $book['description']; ?></p>
        <p><strong>Pris: <?php echo $book['price']; ?> SEK</strong></p>

        <button id="pay">Köp boken</button>
    </div>

    <script>
        const btn = document.getElementById('pay');
        let stripe = null, elements = null;

        btn.addEventListener("click", async (event) => {
    // När användaren trycker på "Köp boken" ska vi skapa en Stripe betalning
    if(btn.innerText === "Köp boken"){
        btn.innerText = "Betala";

        // Hämta clientSecret från vår backend (create-payment-intent.php)
        const response = await fetch('create-payment-intent.php?price=<?php echo $book['price']; ?>&title=<?php echo urlencode($book['title']); ?>');
        const { clientSecret, error } = await response.json();

        if (error) {
            console.error(error);
            alert('Något gick fel, försök igen senare.');
            return;
        }

        const appearance = {
            theme: 'stripe',
            variables: {
                colorPrimary: 'lightseagreen',
                borderRadius: '5px',
                fontFamily: 'Comic Sans, sans-serif',
                colorBackground: '#fafafa'
            }
        };

        // Skapa Stripe objektet och definiera element
        stripe = Stripe(STRIPE_PUBLIC_KEY);
        elements = stripe.elements({ clientSecret, appearance });

        const address = elements.create('address', { mode: 'shipping' });
        address.mount('#addressElement');

        const payment = elements.create('payment', {
            layout: {
                type: 'tabs',
                defaultCollapsed: false
            }
        });
        payment.mount('#paymentElement');
    }

    // När användaren trycker på Betala knappen, genomför betalningen
    const result = await stripe.confirmPayment({
        elements, 
        confirmParams: {
            return_url: 'http://localhost:8080/stripe/success.php' // Den här URLen kan vara din success-sida
        }
    });

 
});
    </script>

    <!-- Här kan vi visa Stripe Elements som krävs för att genomföra betalningen -->
    <div id="addressElement"></div>
    <div id="paymentElement"></div>
    <div id="errorMessage"></div>
  
</body>
</html>