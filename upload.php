<?php
	// controllo che non ci siano stati errori nell'upload (codice = 0) 
	if ($_FILES['uploadfile']['error'] == 0)
	{
		// upload ok
		// copio il file dalla cartella temporanea a quella di destinazione mantenendo il nome originale 
		copy($_FILES['uploadfile']['tmp_name'], "C:/Program Files (x86)/EasyPHP 2.0b1/www/ConfrontoParentele/".$_FILES['uploadfile']['name']) or die("Impossibile caricare il file");
	    echo "Il file &egrave; stato correttamente importato sul server<br><br>";
	    // upload terminato, stampo alcune info sul file
		//echo "Nome file: ".$_FILES['uploadfile']['name']."<br>";
		//echo "Dimensione file: ".$_FILES['uploadfile']['size']."<br>";
		//echo "Tipo MIME file: ".$_FILES['uploadfile']['type'];
    }
    else
    {
	   // controllo il tipo di errore
	   if ($_FILES['uploadfile']['error'] == 2)
	   {
		// errore, file troppo grande (> 1MB)
		die("Errore, file troppo grande: il massimo consentito &egrave; 1MB");
       }
	   else
	   {
		// errore generico
		die("Errore, impossibile caricare il file");
	   }
    }

	$host_name='localhost';
	$user_name='root';
	$conn=@mysql_connect($host_name,$user_name,'')
		or die ("<BR>Impossibile stabilire una connessione con il server ed inserire nel database i dati importati");
	@mysql_select_db('confronto_parentele')
		or die ("Impossibile selezionare il database di confronto archivi, chiudere il programma e riprovare");

	//query che mi permette di capire se sto importando il file 1 o il file 2
	$query1="SELECT *
			  FROM locale
			  limit 1";
	$risultato1=mysql_query($query1)
		or die("Verifica composizione archivio 1 fallita: ".mysql_error());
	$righe1=mysql_num_rows($risultato1);

	//Creo una variabile con il file CSV da importare e lo importo, mostrandone poi a video il contenuto
	if ($righe1==0)
		{
			$CSVFile="C:/Program Files (x86)/EasyPHP 2.0b1/www/ConfrontoParentele/".$_FILES['uploadfile']['name'];
			$importazione=mysql_query("LOAD DATA LOCAL INFILE '" .$CSVFile. "' INTO TABLE locale FIELDS TERMINATED BY ','")
				or die ("Impossibile caricare i dati nella tabella locale");
			
			//controllo che nella tabella del Cliente non ci siano parentele con codiceapr4 diverso rispetto a quello della tabella ministeriale. Se ci sono, blocco il programma e costringo l'utente a sistemare i codici apr4 sbagliati per poi ricaricare il file
			$query="select count(*) 
					from locale
					where codiceapr4 not in
						  (select id
						   from ministero
						  )";
			$risultato=mysql_query($query)
				or die("Impossibile verificare che nella tabella del Cliente non ci siano parentele con codiceapr4 diverso rispetto a quello della tabella ministeriale: ".mysql_error());
			$quantiErrati=mysql_fetch_row($risultato);
			
			//se la query precedente produce risultato>0, blocco l'esecuzione del programma
			if($quantiErrati[0]>0)
			{
				echo('<font color="red"><h2>Attenzione!! Nella tabella caricata sono presenti record dove il campo "codiceapr4" non corrisponde con i codici della tabella ministeriale: sistemare i dati della tabella locale, riesportarla e riprovare.</h2></font>
				<table border=1 width="100%">
					<tr>
						<td width="50%"><div align="center"><b>Codici APR4 tabella ministeriale:</b></div></td>
						<td width="50%"><div align="center"><b>Relazioni di parentela della tabella locale con codice apr4 non accettato:</b></div></td>
					</tr>
					<tr>
					<tr>
						<td width="50%">');
							$query="select * 
									from ministero";
							$risultato=mysql_query($query)
								or die("Impossibile estrarre i dati della tabella ministeriale: ".mysql_error());
							$colonne=mysql_num_fields($risultato);
							echo("<div align='center'><br>");
							echo("<table border=1>");
								echo("
								<tr>
									<td><b>Id</b></td><td><b>TipoParentela</b></td>
								</tr>
								");
							while($riga=mysql_fetch_row($risultato))
							{
								echo("<tr>");
								for($j=0;$j<$colonne;$j++)
									echo("<td>".$riga[$j]."</td>");
								echo("</tr>");
							}
							echo("</table><br>");
							echo("</div>");
						echo("</td>");
						echo("<td width='50%' valign='top'>");
							$query="select sigla, tipoparentela, codiceapr4
									from locale
									where codiceapr4 not in
										  (select id
										  from ministero);";
							$risultato=mysql_query($query)
								or die("Impossibile estrarre le parentele con codice apr4 non accettabile: ".mysql_error());
							$colonne=mysql_num_fields($risultato);
							echo("<div align='center'><br>");
							echo("<table border=1>");
								echo("<tr>
										<td><b>Sigla</b></td><td><b>TipoParentela</b></td><td><b>CodiceAPR4</b></td>
									  </tr>");
								while($riga=mysql_fetch_row($risultato))
								{
									echo("<tr>");
									for($j=0;$j<$colonne;$j++)
										if($j==2)
											echo("<td><font color='red'><b><div align='center'>".$riga[$j]."</div></b></font></td>");
										else
											echo("<td>".$riga[$j]."</td>");
									echo("</tr>");
								}
							echo("</table>");
							echo("</div>");
						echo("</td>");
					echo("</tr>");
				echo('</table>');
				echo("<br><br>");
				echo('<table border=0 width="100%">
						<tr>
							<td width="50%"><a href="cancellazioneArchivi.php">Cancellazione tabella locale caricata</a></td>
							<td width="50%"><div align="right"><a href="index.php">Torna alla pagina iniziale</a></div></td>
						</tr>
					  </table>');
			}
			else
			{
				$query="select * 
						from locale";
				$risultato=mysql_query($query)
					or die("Impossibile recuperare il contenuto dell'archivio importato: ".mysql_error());
				$colonne=mysql_num_fields($risultato)
					or die("Impossibile calcolare quante colonne ha la tabella locale: ".mysql_error());
				$clienteDigitato=strtoupper($_POST["cliente"]);
				$cliente=addslashes($clienteDigitato);
				$queryCliente="update cliente
				   set cliente='$cliente'";
				$risultatoCliente=mysql_query($queryCliente)
					or die("Impossibile aggiornare tabella 'cliente' con il nome del Cliente digitato: ".mysql_query());
				echo("<div align='center'>");
				echo("<b>Ecco il contenuto dell'archivio importato per il Cliente ".$clienteDigitato.":</b><br><br>");
				echo("<table border=1>");
					echo("
					<tr>
						<td><b>Sigla</b></td><td><b>TipoParentela</b></td><td><b>CodiceAPR4</b></td><td><b>OrdineLista</b></td><td><b>TipoSesso</b></td>
					</tr>
					");
					while($riga=mysql_fetch_row($risultato))
					{
						echo("<tr>");
						for($j=0;$j<$colonne;$j++)
							echo("<td>".$riga[$j]."</td>");
						echo("</tr>");
					}
				echo("</table>");
				echo("</div>");
				echo('<br><br>
					  <table border=0 width="100%">
						<tr>
							<td width="50%"><a href="confrontoParentele.php">Componi il confronto con la tabella ministeriale</a></td>
							<td width="50%"><div align="right"><a href="index.php">Torna alla pagina iniziale</a></div></td>
						</tr>
					  </table>
					');
			}
		}
	else
	   {
		echo("Ma la tabella 'locale' non &egrave; vuota, per cui non &egrave; possibile caricare le nuove relazioni di parentela.<br><br>");
		echo('<a href="cancellazioneArchivi.php">Cancellazione tabella locale caricata</a>');
	   }
	mysql_close($conn);
	
?>