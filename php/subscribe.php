<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Save to a text file (simple approach)
    file_put_contents('subscribers.txt', $email.PHP_EOL, FILE_APPEND);
    
    // OR send to your email
    mail('marcos@marcosmurilocampos.com', 'New Subscriber', $email);
    
    // Return success response
    echo "Thank you for subscribing!";
}
?>