

<?php
include_once 'config.php';

$filepath = realpath('folder/.attheme');
$post = array('chat_id' => $GLOBALS["chat_id"],'document'=>new CurlFile($filepath));    
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://api.telegram.org/bot" . $GLOBALS["token"] . "/sendDocument");
curl_setopt($ch, CURLOPT_POST, 1);   
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_exec ($ch);
curl_close ($ch); 



// start coding variables
$update = json_decode(file_get_contents('php://input'));
$text = $update->message->text;
$chat_id = $update->message->chat->id;
$from_id = $update->message->from->id;
$chatid = $update->callback_query->message->chat->id;
$data = $update->callback_query->data;

// creat database connection_aborted
$conn = mysqli_connect('localhost',$db_username,$db_password,$db_name);
mysqli_set_charset($conn,"utf8mb4");

// start main coding

$getUser = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE user_id='$from_id'"));

if(!$getUser)
	mysqli_query($conn,"INSERT INTO users (user_id,account,time) VALUES ('$from_id','no','0')");


elseif($data == 'about'){
    SendMessage($chatid,"متن درباره ما اینجا برای تست");
}

	
	if($getUser['account'] != 'no'){
		$time1 = time();
		$time2 = $getUser['time'];
		$mande = $time2 - $time1;
		if($mande > 0){
			$mande = $mande / 24 / 3600;
			$mande = round($mande);
			SendMessage($chat_id,"شما اکنون دارای یک حساب ویژه ".$getUser['account']." میباشید که ".$mande." روز از ان باقی مانده است. با خرید اشتراک جدید اشتراک قبلی شما از بین خواهد رفت");
		}
	}


elseif(in_array($chat_id,$admins)){
	if($text == '/panel'){
		SendMessage($chat_id,"یک گزینه انتخاب کنید",json_encode(['keyboard' => [
		[['text' => 'مشاهده اکانت ها']]
		],'resize_keyboard' => true ]));
	}
	elseif($text == 'مشاهده اکانت ها'){
		$sql = "SELECT * FROM users WHERE account!='no' ORDER BY time";
		$query = mysqli_query($conn,$sql);
		if($query->num_rows > 0){
			while($fetch = mysqli_fetch_assoc($query)){
				$all[] = $fetch;
			}
			foreach($all as $target){
				$user = $target['user_id'];
				$time1 = $target['time'];
				$account = $target['account'];
				$time2 = time();
				$mande = $time1 - $time2;
				$mande = $mande / 24 / 3600;
				$mande = round($mande);
				$message .= "\n کاربر <a href='tg://user?id=$user'>$user</a> دارای اکانت $account و $mande روز باقی";
			}
			SendMessage($chat_id,$message);
		}else
			SendMessage($chat_id,"تا کنون کسی اکانت تهیه نکرده است");
	}
}
			

?>
