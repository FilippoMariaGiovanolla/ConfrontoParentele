<html>	
	<head>
		<title>Confronto parentele</title>
	</head>
	<body>
		<div align="center"><b><h3>Confronto relazioni di parentela</h3></b></div>
		<?php
			$host_name='localhost';
			$user_name='root';
			$conn=@mysql_connect($host_name,$user_name,'')
				or die ("<BR>Impossibile stabilire una connessione con il server: ".mysql_error());
			@mysql_select_db('confronto_parentele')
				or die ("Impossibile selezionare il database di confronto archivi, chiudere il programma e riprovare: ".mysql_error());
			echo("<ul>");
				$query="select * 
					    from locale";
				$risultato=mysql_query($query)
					or die("Impossibile eseguire la query di selezione dalla tabella 'locale': ".mysql_error());
				$numRigheLocale=mysql_num_rows($risultato);
				//echo('Numero righe tabella "locale": '.$numRigheLocale.'<br>');
				if($numRigheLocale<=0)
				{
					echo('<li><a href="importDati.php">Caricamento file da confrontare con tabella ministeriale</a></li>');
					echo('<li>Componi confronto con tabella ministeriale</li>');
					echo('<li>Cancellazione tabella cliente gi&agrave; caricata</li>');
				}
				else
				{
					$query="select cliente
							from cliente";
					$risultato=mysql_query($query)
						or die("Impossibile estrarre il Cliente di cui &egrave; sono state caricate le parentele da confrontare: ".mysql_error());
					echo('<li>Caricamento file da confrontare con tabella ministeriale (passo gi&agrave; eseguito)</li>');
					while($riga=mysql_fetch_row($risultato))
						echo('<li><a href="confrontoParentele.php">Componi confronto con tabella ministeriale per '.$riga[0].'</a></li>');
					echo('<li><a href="cancellazioneArchivi.php">Cancellazione tabella Cliente gi&agrave; caricata</a></li>');				
				}				
			echo("</ul>");
		?>
		<?php
			mysql_close($conn);
		?>
	</body>
</html>