<?php 
$a=microtime(true);
$b = uniqid(mt_rand(), true);
$c = str_pad(mt_rand(0, 9999999999), 6, '0', STR_PAD_LEFT);
var_dump($a, $b, $c);

