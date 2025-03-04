<?php

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";

if($_SERVER["SERVER_ADDR"]=="172.26.21.203" || strpos(strtolower($_SERVER['HTTP_HOST']),'hillpubadmin34')!==false)
{
    $dbPass = "!eric123";
    define('dbHost','localhost');
    define('dbUser','root');
    define('dbPass','!eric123');
    define('OauthDSN','mysql:dbname=oauth;host=localhost');
}
else
{
	define('dbHost','localhost');
	define('dbUser','root');
	$dbPass = '!eric123';
	define('dbPass','!eric123');
	define('OauthDSN','mysql:dbname=oauth;host=localhost');
}

?>
