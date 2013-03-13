<html>
<head>
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
   <title><?php echo $subiectMail; ?></title>
</head>
<body>

<p><?php echo $apelare ?></p>

<p>Vă anunțăm că listele de plată pentru luna <?php echo $lunaListaPlata.' '.$anListaPlata ?>, de la <?php echo $adresaProprietar ?> au fost 
<?php if(isset($reprocesare) && $reprocesare) : ?>
	actualizate.
<?php else : ?>
	trimise spre afișare.
<?php endif; ?>
</p>
<p>Varianta eletronică poate fi consulatată și accesand <a href="<?php echo $urlPrimaPagina ?>">prima pagină</a> din contul dvs. URBICA.</p>

<p>O zi bună în continuare,<br />
Echipa Urbica
</p>
<table>
<tbody><tr>
<td><img src="http://www.urbica.ro/_view/_images/sigla-urbica-72dpi.png" alt="URBICA" title="URBICA"></td>
<td style="color: gray; font-family: sans-serif; font-size: 90%; vertical-align: middle;">
<p>E-mail: </p>
<p>Internet: </p>
<p>Telefon: </p>
<p>Adresă: </p>
</td>
<td style="color: gray; font-family: sans-serif; font-size: 90%; vertical-align: middle;">
<p><a style="text-decoration: none; color: gray;" href="mailto:contact@urbica.ro">contact@urbica.ro</a></p>
<p><a style="text-decoration: none; color: gray;" href="http://www.urbica.ro" target="_blank">http://www.urbica.ro</a></p>
<p>+40 332 411 555</p>
<p>Iași, str. Cuza Vodă nr. 13</p>
</td>
</tr>
</tbody>
</table>

</body>
</html>