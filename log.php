<?php

if (!function_exists('json_encode')) {
	require_once 'JSON/JSON.php';

	function json_encode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->encode($arg);
	}

	function json_decode($arg)
	{
		global $services_json;
		if (!isset($services_json)) {
			$services_json = new Services_JSON();
		}
		return $services_json->decode($arg);
	}
}

function myLog() {
if(!isset($_GET['link']) || $_SESSION['login'] == '') return;

$log_a =
	isset($_GET['asoc_id'])  ? $_GET['asoc_id'] :
	(isset($_POST['asoc_id']) ? $_POST['asoc_id'] :
	0);

$log_s =
	isset($_GET['scara_id'])  ? $_GET['scara_id'] :(
	isset($_POST['scara_id']) ? $_POST['scara_id'] :
	0);

$log_l =
	isset($_GET['loc_id'])  ? $_GET['loc_id'] :(
	isset($_POST['loc_id']) ? $_POST['loc_id'] :(
	isset($_GET['locatar']) ? $_GET['locatar'] :(
	(isset($_GET['edit']) && isset($_GET['link']) && $_GET['link']=='locatari') ? $_GET['edit'] :(
	(isset($_GET['editeaza']) && isset($_GET['link']) && $_GET['link']=='locatari_apometre') ? $_GET['editeaza'] :(
	0)))));


$log_get = mysql_real_escape_string(str_replace('"', '', json_encode($_GET)));
$log_post = mysql_real_escape_string(str_replace('"', '', json_encode($_POST)));

$log_sql = 'INSERT INTO app_log  (`id`, `time`, `link`, `user_id`, `asoc_id`, `scara_id`, `loc_id`, `get`, `post`, `ip`) VALUES
								 (NULL, CURRENT_TIMESTAMP, "'.(isset($_GET['link']) ? mysql_real_escape_string($_GET['link']) : 'NULL').'", "'.(isset($_SESSION['rank']) ? mysql_real_escape_string($_SESSION['rank']) : "NULL") .'", "'.$log_a.'", "'.$log_s.'", "'.$log_l.'", "'.$log_get.'", "'.$log_post.'", "'.$_SERVER['REMOTE_ADDR'].'");';


mysql_query($log_sql);
}
?>