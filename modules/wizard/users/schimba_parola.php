<?php
	if (isset($_POST['OK'])){
            
		$pass = md5($_POST['old_password']);
		$new_pass = $_POST['new_password'];
		$check_pass = $_POST['retype_password'];
		$rank = $_SESSION['rank'];
		
		$sql = "SELECT * FROM admin WHERE id=".$rank;
		$sql = mysql_query($sql) or die("Nu m-am putut conecta la baza de date. <br />".mysql_error());
		
		$parola_db = mysql_result($sql, 0, 'pass');
		if ($pass != $parola_db){
			echo '<div style="color:red">Parola introdusa de dumneavoastra nu este corecta<br /></div>';
		} else {
                    if ($new_pass != $check_pass){
			echo '<div style="color:red">Parola din campul "Verificare", nu corespunde cu "Noua Parola"<br /></div>';
                    } else {
			if (strlen($new_pass)<8) {
                            echo '<div style="color:red">Parola trebuie sa aiba minim 8 caractere<br /></div>';
			} else {
                            $sql1 = "UPDATE admin SET pass='".md5($new_pass)."' WHERE id=".$rank;
                            $sql1 = mysql_query($sql1) or die("Nu am putut modifica parola. <br />".mysql_error());
				
                            echo '<div style="color:red">Parola a fost schimbata cu succes!<br /></div>';
			}
                    }
		}
	}
?>
<div align="left">
	<form action="" method="post">
		<table>
			<tr>
				<td>Parola Actuala:</td>
				<td>&nbsp;</td>
				<td><input type="password" name="old_password" /></td>
			</tr>
			<tr>
				<td>Noua Parola:</td>
				<td>&nbsp;</td>
				<td><input type="password" name="new_password" /></td>
			</tr>
			<tr>
				<td>Verificati Parola:</td>
				<td>&nbsp;</td>
				<td><input type="password" name="retype_password" /></td>
			</tr>
                </table>
		<input type="submit" name="OK" value="Schimba parola" />
	</form>
</div>