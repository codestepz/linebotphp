<?php

// ตั้งค่า
$setting = include_once('setting.php');

// CURL
$ch = curl_init($setting->urlVerify);
		
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Authorization: Bearer ' . $setting->access_token ]);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

$result = curl_exec($ch);
curl_close($ch);

// Echo
echo print_r($result);