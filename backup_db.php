<?php

$table_furnizori = array();
$table_setari = array();
$table_data = array();
$table_data []= 'apometre';
$table_setari []= 'asociatii';
$table_furnizori []= 'asociatii_furnizori';
$table_setari []= 'asociatii_setari';
$table_data []= 'casierie';
$table_furnizori []= 'facturi';
$table_data []= 'fisa_cont';
$table_data []= 'fisa_fonduri';
$table_data []= 'fisa_fonduri_completare';
$table_furnizori []= 'fisa_furnizori';
$table_data []= 'fisa_indiv';
$table_data []= 'fisa_pen';
$table_setari []= 'lista_plata';
$table_setari []= 'locatari';
$table_setari []= 'locatari_apometre';
$table_setari []= 'locatari_datorii';
$table_setari []= 'scari';
$table_furnizori []= 'scari_furnizori';
$table_setari []= 'scari_setari';
$table_furnizori []= 'subventii';
$table_furnizori []= 'furnizori';
$table_furnizori []= 'servicii';
$table_furnizori []= 'furnizori_servicii';


backup_tables('localhost','rurb4601','Urbica2009','rurb4601_urb',$table_furnizori);
backup_tables('localhost','rurb4601','Urbica2009','rurb4601_urb',$table_setari);
backup_tables('localhost','rurb4601','Urbica2009','rurb4601_urb',$table_data);




/* backup the db OR just a table */
function backup_tables($host,$user,$pass,$name,$tables = '*')
{

	$link = mysql_connect($host,$user,$pass);
	mysql_select_db($name,$link);

	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	//cycle through
	foreach($tables as $table)
	{
		echo 'Salvez tabela '.$table."<br />\n";
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);

		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";

		for ($i = 0; $i < $num_fields; $i++)
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++)
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}

	//save file
	echo 'Salvez fisierul '.'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql'."<br />\n";
	//$handle = fopen('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	//fwrite($handle,$return);
	//fclose($handle);

	$zip = new ZipArchive();
	$filename = date('Y-m-d').'-db_backup-'.(md5(implode(',',$tables))).'.zip';

	if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
		exit("cannot open <$filename>\n");
	}

	$zip->addFromString('db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql', $return);
	//$zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
	//$zip->addFile($thisdir . "/too.php","/testfromfile.php");
	echo "numfiles: " . $zip->numFiles . "<br />\n";
	echo "status:" . $zip->status . "<br />\n";
	$zip->close();

	echo 'GATA !!!';
}


?>