<?php

/*

به نام خدا
سورس ربات نوشته شده توسط: http://t.me/goldsudo
کانال نویسنده: http://t.me/feelphp
لطفا حقوق نویسنده را رعایت کنید

*/


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

$time = time();
$sql = "SELECT user_id FROM users WHERE account!='no' AND (users.time) <= $time";
$query = mysqli_query($conn,$sql);

if($query->num_rows > 0){
	while($fetch = mysqli_fetch_assoc($query)){
		$all[] = $fetch;
	}
	
	foreach($all as $target){
		
		$user = $target['user_id'];
		file_get_contents('http://api.telegram.org/bot'.$API_KEY.'/kickchatmember?chat_id='.$channel.'&user_id='.$user);
		SendMessage($user,"روزهای اکانت شما به پایان رسید و شما از کانال ریمو کنید. برای ورود مجدد اقدام به تهیه اکانت فرمایید");
		mysqli_query($conn,"UPDATE users SET account='no' WHERE user_id='$user'");
		foreach($admins as $admin){
			SendMessage($admin,"اکانت کاربر  <a href='tg://user?id=$user'>$user</a> به پایان رسید و از کانال ریمو شد.");
		}
	}
	echo 'ok';
}else
    echo 'false';

?>