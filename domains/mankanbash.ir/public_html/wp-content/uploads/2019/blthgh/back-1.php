<?php

/*

به نام خدا
سورس ربات نوشته شده توسط: http://t.me/goldsudo
کانال نویسنده: http://t.me/feelphp
لطفا حقوق نویسنده را رعایت کنید

*/

if(!is_numeric($_GET['user']) or !isset($_GET['Authority']) or !isset($_GET['Status'])) die;

include_once 'config.php';

// creat database connection_aborted
$conn = mysqli_connect('localhost',$db_username,$db_password,$db_name);
mysqli_set_charset($conn,"utf8mb4");

define('API_KEY',$API_KEY);
function bot($method, $datas = []){
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}
function SendMessage($chat_id, $text, $key = null){
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'Html',
        'disable_web_page_preview' => true,
        'reply_markup' => $key
    ]);
}

$Authority = $_GET['Authority'];
$user = $_GET['user'];
$Amount = $one_hour_amount;

if ($_GET['Status'] == 'OK'){
	$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']);
	$result = $client->PaymentVerification([
	'MerchantID' => $zarinpal,
	'Authority' => $Authority,
	'Amount' => $Amount,
	]);
	if ($result->Status == 100){
		$getUser = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE user_id='$user'"));
		$time = time() + 1 * 3600;
		mysqli_query($conn,"UPDATE users SET account='یک ماهه', time='$time' WHERE user_id='$user'");
		echo 'successfull payment. channel link sent for you in telegram!';
		$url = json_decode(file_get_contents('http://api.telegram.org/bot'.$API_KEY.'/getchat?chat_id='.$channel),true);
		$link = $url['result']['invite_link'];
		SendMessage($user,"پرداخت با موفقت انجام شد.
کاربر گرامی به محض دریافت لینک وارد کانال شوید و به هیچ وجه خارج نشوید،اشتراک شما باطل خواهد شد
❌بعد چند دقیقه لینک باطل خواهد شد",json_encode(['inline_keyboard' => [
		[['text' => 'ورود به کانال', 'url' => $link ]]
		]]));
		foreach($admins as $admin){
			SendMessage($admin,"کاربر <a href='tg://user?id=$user'>$user</a> یک اشتراک یک ساعته خریداری کرد.");
		}
	}else
		echo 'unsuccessfull payment!';
}else
	echo 'payment was canceled by user!';
?>