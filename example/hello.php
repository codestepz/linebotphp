<?php

// เรียกไฟล์ Library
include ('vendor/autoload.php');

// ตั้งค่า
$setting = include_once('./setting.php');

// เชื่อมต่อ Library
$bot = new \Codestep\LineBotPhp($setting->channelSecret, $setting->access_token);

// ส่งข้อมูลให้ตัวเอง
$bot->sendMessageNew($setting->userId, 'Hello World !!');

// สำเสร็จ
if ($bot->isSuccess()) { echo 'Succeeded!'; exit(); }

// ไม่สำเร็จ
echo $bot->response->getHTTPStatus . ' ' . $bot->response->getRawBody(); 
exit();