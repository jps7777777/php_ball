<?php
error_reporting(E_ERROR);
require_once 'phpqrcode/phpqrcode.php';
$url = urldecode($data);
QRcode::png($url);
