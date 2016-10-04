<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');  



$channel 			= @$_POST['channel'];
$provider 			= @$_POST['provider'];
$subject 			= @$_POST['subject'];
$sender 			= @$_POST['sender'];
$recipient 			= @$_POST['recipient'];
$body 				= @$_POST['body'];
$bodyVariablesCSV	= @$_POST['bodyVariablesCSV'];
$bodyVariablesJSON	= @$_POST['bodyVariablesJSON'];

echo var_dump($_SERVER);
echo $_SERVER['HTTP_HOST'];
echo "  ";
echo $_SERVER['REMOTE_ADDR'];

?>