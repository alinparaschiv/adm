<?php
//$linenum = 2; //will read 2nd line of the file.
$path = "error_log";
$handle = fopen($path, "r");
$lines=file($path); $lines = array_reverse($lines); //$lines is an array 

	
//echo htmlentities($lines[$linenum]);
//echo $theData;
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">
body { font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size:12px; }
b { color:#333; font-weight:bold;}
span { padding:3px; margin:2px; background-color:#EEE; color:#999; float:left; width:100%; border:solid 1px #DDD; }
</style>
<title>Untitled Document</title>
</head>

<body>
<?php
$i = 1;
foreach($lines as $line) {
	echo "<span>".$i.". ".$line."</span><br clear='all' />";
	$i++;
}
?>


</body>
</html>