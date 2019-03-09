<?php
/*

به نام خدا
سورس ربات نوشته شده توسط: http://t.me/goldsudo
کانال نویسنده: http://t.me/feelphp
لطفا حقوق نویسنده را رعایت کنید

*/

// اسکریپت تغییر لینک خصوصی کانال

include_once 'config.php';

$get = json_decode(file_get_contents("http://api.telegram.org/bot".$API_KEY."/exportchatinvitelink?chat_id=".$channel));

if($get->ok == 'true')
    echo 'true';
else
    echo 'false';
?>