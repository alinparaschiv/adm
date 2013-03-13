<?php
session_start();
if (!isset($_SESSION['login'])) {
	header('Location: /app/');
}
error_reporting(-1);
ini_set('display_errors', 'stdout');
date_default_timezone_set('Europe/Bucharest');
?>

<html>
<head>
<meta charset="UTF-8"/>
<style type="text/css">
body, form, input, table > tr > td {
	font-size: 24px;
}
</style>
</head>
<body>
<?php
require('_controller/verificare_alte_apartamente.php');
require('_controller/actualizare_proprietar.php');
?>
</body>
</html>