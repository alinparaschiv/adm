<?php

if(isset($_POST['Adauga'])) {
$nume = $_POST['Nume'];
$user = $_POST['username'];
$acces = $_POST['acces'];
$mail = $_POST['mail'];
$cnp = $_POST['cnp'];
$buletin = $_POST['buletin'];
$politia = $_POST['politia'];
$telefon = $_POST['telefon'];

$access = explode("-", $acces);

//Trimite mail
function trimite_mail($mail, $user, $pass){
$header = "From: Urbica <office@urbica.ro>\r\n";
$mesaj = "Buna ziua.\n\n
Contul dumneavoastra a fost creat.\n
Datele de autentificare:\n
User: ".$user."\n
Parola: ".$pass;
mail($mail, 'Contul pe www.urbica.ro a fost creat', $mesaj, $header);
}

//Generare Parola
function generatePassword($length=8,$level=3){

   list($usec, $sec) = explode(' ', microtime());
   srand((float) $sec + ((float) $usec * 100000));

   $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
   $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
   $validchars[3] = "0123456789_!@#$%&*()-=+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_!@#$%&*()-=+/";

   $password  = "";
   $counter   = 0;

   while ($counter < $length) {
     $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);

     // All character must be different
     if (!strstr($password, $actChar)) {
        $password .= $actChar;
        $counter++;
     }
   }
   return $password;
}


$pass = generatePassword(8,3);


//Verific E-Mail
function is_valid_email($email)
{
	return preg_match( '/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-]+.[a-zA-Z]+/', $email );
}

//verificare
if (($nume == '') || ($user == '') || ($mail == '') || ($cnp == '') || ($buletin == '') ||($politia == '') || ($telefon == ''))
	{
		echo '<div style="color:red">Toate campurile trebuiesc completate<br /></div>';
	}
	else
	{
		if (!is_valid_email($mail)) {
			echo '<div style="color:red">Introduceti o adresa de e-mail valida<br /></div>';
		} else
		if (!ctype_digit($cnp)){
			echo '<div style="color:red">CNP-ul trebuie sa fie format numai din cifre<br /></div>';
		} else {
			$sql = "SELECT * FROM admin WHERE user='".$user."'";
			$sql = mysql_query($sql) or die("Nu m-am putut conecta la baza de date <br />".mysql_error());

			if (mysql_num_rows($sql) != 0)
			{
				echo '<div style="color:red">Userul exista in baza de date.<br /></div>';
			}
			else
			{
				$sql = " INSERT INTO admin VALUES(null, '".$nume."', '".$user."', '".md5($pass)."', $access[0], '".$access[1]."', '".$mail."', '$cnp', '$buletin', '$politia', '$telefon')";
				$sql = mysql_query($sql) or die("Nu am putut scrie in baza de date <br />".mysql_error());

				trimite_mail($mail, $user, $pass);
				echo '<div>Userul a fost creat cu succes.<br /> Parola a fost trimisa pe adresa de E-mail furnizata de dumneavoastra.<br /></div>';
			}
		}
	}
}


?>
<div align="left">
<form action="" method="post">
    <table>
        <tr>
            <td> Nume si Prenume </td>
            <td>&nbsp; </td>
            <td> <input type="text" name="Nume" /></td>
        </tr>

        <tr>
            <td> UserName </td>
            <td>&nbsp; </td>
            <td> <input type="text" name="username" /></td>
        </tr>

<tr>
            <td> Nivel Acces </td>
            <td>&nbsp; </td>
            <td>
                <select id="acces" name="acces">
                    <option value="0 - Super Administrator">Super Administrator</option>
                    <option value="1 - Operator Sef">Operator Sef</option>
                    <option value="2 - Operator">Operator</option>
                    <option value="3 - Administrator">Administrator</option>
                    <option value="4 - Coordonator Economic">Coordonator Economic</option>
                    <option value="5 - Casier">Casier</option>
                    <option value="6 - Relatii Furnizori">Relatii Furnizori</option>
                </select>
			</td>
   	  </tr>

		<tr>
			<td> CNP </td>
			<td> &nbsp; </td>
			<td> <input type="text" name="cnp" /> </td>
		</tr>

		<tr>
			<td> CI / BI (Serie/Nr) </td>
			<td> &nbsp; </td>
			<td> <input type="text" name="buletin" /> </td>
		</tr>

		<tr>
			<td> Eliberat de </td>
			<td> &nbsp; </td>
			<td> <input type="text" name="politia" /> </td>
		</tr>
		<tr>
			<td> Telefon</td>
			<td> &nbsp; </td>
			<td> <input type="text" name="telefon" /> </td>
		</tr>

        <tr>
            <td> E-mail </td>
            <td>&nbsp; </td>
            <td><input type="text" name="mail" /></td>
        </tr>

        <tr>
            <td colspan="3">*) Toate campurile sunt obligatorii</td>
        </tr>
    </table>
    <input name="Adauga" type="submit" value="Adauga" />
</form>
</div>