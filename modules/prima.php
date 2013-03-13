<div class="clearfix" align="left">
<code>
   <?php
   if ($_SESSION['uid'] == 0) {
   	$users = array(27, 32, 6, 30);

   	foreach ($users as $user) {
   	$s = '
SELECT u.nume as casier, log.*, a.asociatie, s.scara, l.nume, l.ap
FROM admin u, app_log log
LEFT OUTER JOIN asociatii a ON a.asoc_id=log.asoc_id
LEFT OUTER JOIN scari s ON s.scara_id=log.scara_id
LEFT OUTER JOIN locatari l ON l.loc_id=log.loc_id
WHERE log.user_id='.$user.' AND log.user_id=u.id
ORDER BY id DESC LIMIT 10';
	 $q = mysql_query($s) or die('Nu pot afla date din DB');

     while ($row = mysql_fetch_assoc($q)) {
       echo '['. $row['time'].' ][ '.str_pad($row['casier'], 20, "_").' ][ '. $row['link'].' ]';
        if($row['asociatie']) echo '['.str_pad($row['asociatie'], 0, "_").']';
     	if($row['scara']) echo '['.str_pad($row['scara'], 0, "_").']';
     	if($row['nume']) echo '['.str_pad($row['nume'].' Ap. '.$row['ap'], 0, "_").']';

       echo '<br />';
     }
   	 echo '<br /><br /><br />';
   }
   }
   ?>
   </code>
</div>