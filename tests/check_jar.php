<?php

$cookieJar = tempnam(sys_get_temp_dir(), 'test_cookie_');
$ch = curl_init('http://192.168.1.118:8080/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
curl_exec($ch);
curl_close($ch);

echo "JAR CONTENTS:\n" . file_get_contents($cookieJar) . "\n";
@unlink($cookieJar);

