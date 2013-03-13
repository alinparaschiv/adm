<table>
<tr>
	<td>Tip persoană: </td>
	<td>
	<form>
		<input type="hidden" name="tip_persoana" value="fizica"/>
		<input type="hidden" name="id_locatar" value="<?php echo $id_locatar; ?>"/>
		<input type="submit" value="fizică"/>
	</form>
	<form>
		<input type="hidden" name="tip_persoana" value="juridica"/>
		<input type="hidden" name="id_locatar" value="<?php echo $id_locatar; ?>"/>
		<input type="submit" value="juridică"/>
	</form>
	</td>
</tr>
</table>
<?php echo $mesaj; ?>
<form id="formular_proprietar" method="post">
<?php echo $camp_ascuns; ?>
<table>
<tr>
	<td><?php echo $titlu_nume1; ?>: </td>
	<td><input type="text" name="nume1" value="<?php echo $nume1; ?>" maxlength="50"/></td>
</tr>
<tr>
	<td><?php echo $titlu_nume2; ?>: </td>
	<td><?php echo $camp_nume2; ?></td>
</tr>
<?php echo $camp_tip; ?>
<tr>
	<td><label>E-mail: </label></td>
	<td><input type="text" name="e_mail" value="<?php echo $e_mail; ?>" maxlength="50"/></td>
</tr>
<tr>
	<td><label>Telefon: </label></td>
	<td><input type="text" name="telefon" value="<?php echo $telefon; ?>" maxlength="20"/></td>
</tr>
<tr>
	<td colspan="2" align="center"><input type="submit" value="<?php echo $denumire_buton; ?>"/></td>
</tr>
</table>
</form>
<!--
<a href="/app/actualizare-proprietar/?id_locatar=<?php echo $_GET['id_locatar'] + 1; ?>"> >> </a>
-->
<script>
window.onload = document.getElementById('formular_proprietar').elements['nume1'].focus();
</script>