<html>
	<head>
		<title>Reset tabella cliente e tabella parentele locale</title>
	</head>
	<body>
	<?php
		$hostname='localhost';
		$username='root';
		$conn=mysql_connect($hostname,$username,'')
			or die("Impossibile stabilire una connessione con il server");
		$db=mysql_select_db('confronto_parentele')
			or die("Impossibile selezionare il database di confronto delle parentele");
		$query="delete from locale";
		$risultato=mysql_query($query)
			or die("Impossibile cancellare il contenuto della tabella 'locale'");
		$query="update cliente set cliente=''";
		$risultato=mysql_query($query)
			or die("Impossibile resettare contenuto della tabella 'cliente'");
		echo("Il contenuto della tabella con le relazioni di parentela del Cliente &egrave; stato correttamente cancellato.");
		mysql_close($conn);
	?>
	<br><br>
	<a href="index.php">Torna alla pagina iniziale</a>
	</body>
</html>