<?php

$path = dirname(__FILE__) . '/';

include($path . '/functions.php');

$url = urldecode($_SERVER['QUERY_STRING']);

if (stristr($url, 'http://') || stristr($url, 'itms://')) {
	st_write_log($path . '/stats/clicks.txt', date('d.m.Y') . '|' . $url);		
	header('location: ' . $_SERVER['QUERY_STRING']);
}

?>
