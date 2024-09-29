
# YooMoney Payments

Accepting payments via YooMoney wallet


## Features

- Payment link generation
- Checking the hash sent to Webhook



## Installation

Copy the yoomoney.php file to the directory with your project

```bash
  git clone https://github.com/polen1kaa/yoomoney-payments
  cd yoomoney-payments
  mv yoomoney.php /var/www/html
```

[Enable HTTP notifications](https://yoomoney.ru/transfer/myservices/http-notification) from YooMoney and generate a secret word for Webhook
## Usage

### Creating a payment link
```php
<?php
include("yoomoney.php");
$yoomoney = new YooMoneyPayments(
  "wallet" => 4100000000000000, # Your YooMoney wallet number
  "secret" => "your-secret-word" # Secret word received when enabling HTTP notifications
);
$answer = $yoomoney->createLink([
  "sum" => 100, # Required: The amount of payment in rubles.
  "label" => 123456, # Optional: Payment ID or any other value you have for recognizing a specific payment on your site
  "successURL" => "https://site.site/success" # Optional: The site to which the client is redirected if the payment is successful.
]);
if($answer["code"] == 200){
  echo $answer["data"] # https://yoomoney.ru/transfer/quickpay?requestId=...
}else{
  echo json_encode($answer); # {"code": 400, "data": "Wow, is this a mistake?"}
}
?>
```

### Webhook handler example
It should receive [notifications from YooMoney](https://yoomoney.ru/transfer/myservices/http-notification) about successful payments.
```php
<?php
include("yoomoney.php");
$yoomoney = new YooMoneyPayments(
  "wallet" => 4100000000000000, # Your YooMoney wallet number
  "secret" => "your-secret-word" # Secret word received when enabling HTTP notifications
);
$answer = $yoomoney->webhookCheck($_POST);
if($answer["code"] == 200){
  echo "Successfully paid payment with label <".$POST["label"].">!"; # {"code": 200, "data": "Successfully paid payment with label <123456>!"}
  
  /*
  Here you can process a successful payment. For example, mark the purchase as paid.
  YooMoney sends a lot of data in addition to the label. Explore the documentation:
  https://yoomoney.ru/docs/payment-buttons/using-api/notifications
  */

}else{
  echo json_encode($answer); # {"code": 400, "data": "Wow, is this a mistake?"}
}
?>
```
