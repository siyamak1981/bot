<?php
/*

به نام خدا
سورس ربات نوشته شده توسط: http://t.me/goldsudo
کانال نویسنده: http://t.me/feelphp
لطفا حقوق نویسنده را رعایت کنید

*/

if(!is_numeric($_GET['month']) or !is_numeric($_GET['user']) or $_GET['month'] != 1 && $_GET['month'] != 2 && $_GET['month'] != 3) die;

include_once 'config.php';

// creat database connection_aborted
$conn = mysqli_connect('localhost',$db_username,$db_password,$db_name);
mysqli_set_charset($conn,"utf8mb4");

$user = $_GET['user'];

if(!mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE user_id='$user'"))) die('user not found!');

switch($_GET['month']){
	case 1:
	$Amount = $one_hour_amount;
	break;
	case 2:
	$Amount = $one_month_amount;
	break;
	case 3:
	$Amount = $two_month_amount;
	break;
}

$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
$result = $client->PaymentRequest(
[
'MerchantID' => $zarinpal,
'Amount' => $Amount,
'Description' => 'خرید برای:'.$user,
'Email' => 'Email@Mail.Com',
'Mobile' => '09123456789',
'CallbackURL' => 'https://'.$domin.'/back-'.$_GET['month'].'.php?user='.$user,
]);
if ($result->Status == 100)
	header("Location: https://www.zarinpal.com/pg/StartPay/".$result->Authority."/ZarinGate");
else
	echo 'failed to creak payment link! please try again later';
?>