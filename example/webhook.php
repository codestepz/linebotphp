<?php

// เรียกไฟล์ Library
include ('vendor/autoload.php');

// ตั้งค่า
$setting = include_once('./setting.php');

// เชื่อมต่อ Library
$bot = new \Codestep\LineBotPhp($setting->channelSecret, $setting->access_token);
	
if (!empty($bot->isEvents)) {
	
    // เพิ่ม displayName
    $this->message->displayName = $bot->displayName;

    // ตอบกลับ
    $bot->replyMessageNew($bot->replyToken, json_encode($bot->message));
    
    // สำเสร็จ
    if ($bot->isSuccess()) { echo 'Succeeded!'; exit(); }

    // ไม่สำเร็จ
    echo $bot->response->getHTTPStatus . ' ' . $bot->response->getRawBody(); 
    exit();

}
