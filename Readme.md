# How to install
`composer install phuminstudio/payment`

# Example use
```php
<?php

use phuminstudio/Payment;

$private_key = "...";
$public_key = "...";

$payment = new Payment($private_key, $public_key);
$payment->kbankSetting("username", "password", "account no.");
$result = $payment->kbankCheck("1", "12", "2019", "13", "59", "300.31");

$payment->scbSetting("username", "password", "account no.");
$result = $payment->scbCheck("1", "12", "2019", "13", "59", "300.31");

$payment->walletSetting("email", "password", "reference_token");
$result = $payment->walletCheck("transaction number");
```