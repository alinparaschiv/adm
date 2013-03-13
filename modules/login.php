<?php
	if(isset($_POST['username']) && isset($_POST['password'])) {
        $user = mysql_real_escape_string($_POST['username']);
if($user == null) {  $error = "Nu ati completat campul username\n";  }
        $password = md5 (mysql_real_escape_string($_POST['password']) );
if($password == null) {  $error .= "Nu ati completat campul password\n";  }
                
        $query = "SELECT * FROM admin WHERE `user`='$user' AND `pass`='$password'";
        $result = mysql_query($query) or die('Eroare #01. Contactati administratorul daca problema persista.');
        while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
                $_SESSION['login'] = 1;
				$_SESSION['user_name'] = $row['nume'];
				$_SESSION['functie'] = $row['functia'];
				$_SESSION['uid'] = $row['user_id'];
				$_SESSION['rank'] = $row['id'];
                ?>
                <script language="javascript">
                     window.location.href = "<?php echo '?link=ok'; ?>"
               </script>
               <?php
        }
	if(mysql_num_rows($result) == 0) {  
 echo ' <script language="javascript">
                     alert("Datele introduse nu sunt corecte!");
               </script>';   }
}
        

?>
<fieldset id="login">
<form method="post" action="#">
	<table>
	<tr>
		<td><img width="48" height="73" src="images/sigla-urbica-verticala-300ppi.png" style="margin-top: 10px;"/></td>
		<td><h3>Autentificare ADM</h3></td>
	</tr>
	<tr>
		<td colspan="2" style="text-align: left;">
			<p><label for="username">Utilizator:</label><br/>
			<input autofocus class="text-input" name="username" type="text" /></p>
			<p><label for="password">Parolă:</label><br/>
			<input class="text-input" name="password" type="password" /></p>
		</td>
	</tr>
	<tr><td colspan="2" style="text-align: right; padding-right: 10px;"><input type="submit" class="button-blue" value="Trimite" /></td></tr>
	</table>
</form>
</fieldset>