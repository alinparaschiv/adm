<?php
$mc = mysqli_connect('localhost', 'rurb4601', 'Urbica2009', 'rurb4601_urb');

if (!$mc) {
	throw new Exception(mysqli_connect_error($mc));
}

if (!mysqli_set_charset($mc, 'utf8')) {
	throw new Exception('Nu pot seta codarea caracterelor la UTF-8.');
}
?>