<html>
	<head>
		<title>Selezione file da importare</title>
	</head>
	<body>
	<center><h3><b>Importazione tabella</b></h3></center>
	<form name="upload" method="post" action="upload.php" enctype="multipart/form-data">
	<fieldset>
		Inserisci il nome del Cliente in esame&nbsp;<input type="text" name="cliente" size=45>
		<br><br>
		<table border=0>
			<tr>
				<td>Seleziona file csv con relazioni di parentela <br> (utilizzare il carattere , come separatore)</td>
				<td valign='middle'>&nbsp;&nbsp;&nbsp;<input type="file" name="uploadfile">
					<a href="esempioCSV.php"><img src="CSV.png" width=20 height=20></a><i>&nbsp;(Scarica file csv di esempio)</i></td>
			</tr>
		</table>
		<br><br>
		<input type="submit" name="go" value="Carica">
	</fieldset>
	</form>
	<br>
	<div align="right"><a href="index.php">Torna indietro</a></div>
	<?php	
	?>
	</body>
</html>