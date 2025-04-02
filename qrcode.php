<?php
include "phpqrcode/qrlib.php";

$text = "https://allivio.com.br";
$file = "qrcode.png";

QRcode::png($text, $file, QR_ECLEVEL_H, 10);


echo '<img src="'.$file.'" />';
