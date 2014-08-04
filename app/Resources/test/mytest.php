<?php 
/*$a=microtime(true);
$b = uniqid(mt_rand(), true);
$c = str_pad(mt_rand(0, 9999999999), 6, '0', STR_PAD_LEFT);
var_dump($a, $b, $c);
*/

// $d = 'bac,dut,master';
// $d = explode(',', $d);
// foreach (explode(',', $d) as $i => $val)$assoc[$val] = $val;
// var_dump($assoc);
// $now = new \DateTime;
// $now = (new \DateTime)->format('d-m-Y_H-i');
// var_dump($now);

$str = '05/01/2014';
// $date = new \DateTime($str);
$date = date_create_from_format('d/m/Y', $str);
var_dump($date);