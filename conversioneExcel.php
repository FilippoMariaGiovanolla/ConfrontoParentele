<html>
	<head>
		<title>Confronto tabelle</title>
	</head>
	<body>
	<?php
		/* questo file è una copia, di fatto, del file confrontoParentele.php, con l'aggiunta, in cima, delle tre linee di codice che seguono, che vanno a migrare su file excel il contenuto della pagina web, proponendo direttamente  il salvataggio del file xls prodotto dalla pagina */
		error_reporting (E_ALL ^ E_NOTICE); // questo comando permette di eliminare dall'output a video le NOTICE indesiderate
		$cliente=$_POST["cliente"];
		$hostname='localhost';
		$username='root';
		$conn=mysql_connect($hostname,$username,'')
			or die("Impossibile stabilire una connessione con il server");
		$db=mysql_select_db('confronto_parentele')
			or die("Impossibile selezionare il database di confronto delle parentele");		

		echo("<div align='center'><b><h3>Risultato confronto relazioni di parentela per ".$cliente."</h3></b></div>");
		$clienteNomeFile=str_replace(" ","_",$cliente);
		//echo("Cliente nome file: ".$clienteNomeFile);
		$filename="ConfrontoParentele".$clienteNomeFile.".xls";
		header ("Content-Type: application/vnd.ms-excel");
		header ("Content-Disposition: inline; filename=$filename");
		//estraggo il nome del Cliente in esame per portarlo a video
		/*$query="select cliente
				from cliente";
		$risultato=mysql_query($query)
			or die("Impossibile estrarre il nome del Cliente in esame: ".mysql_error());			
		while($riga=mysql_fetch_row($risultato))
		{
			echo("<div align='center'><b><h3>Risultato confronto relazioni di parentela per ".$riga[0]."</h3></b></div>");
			$filename="ConfrontoParentele".$riga[0].".xls";
			header ("Content-Type: application/vnd.ms-excel");
			header ("Content-Disposition: inline; filename=$filename");
		}*/
		echo("
			  <table border=0 width='100%'>
				<tr>
					<td width='5%' bgcolor='#98FB98'></td>
					<td width='95%'>In verde sono evidenziati gli eventuali record con corrispondenza esatta tra gli elementi della tabella ministeriale e quelli della tabella locale</td>
				</tr>
				<tr>
					<td width='5%' bgcolor='#FFD700'></td>
					<td width='95%'>In oro sono evidenziati gli eventuali record dove la relazione di parentela della tabella ministeriale &egrave; interamente contenuta nella descrizione della tabella locale.
					</td>
				</tr>
				<tr>
					<td width='5%'></td>
					<td width='95%'>Gli eventuali elementi non evidenziati sono quelli privi di corrispondenza tra la tabella ministeriale e quella della tabella locale</td>
				</tr>
			  </table>
			  <br><br>
			 ");
		
			
		$queryMinistero="select * 
						 from ministero 
						 order by id";
		$risultatoMinistero=mysql_query($queryMinistero)
			or die("Impossibile estrarre i dati dalla tabella 'ministero': ".mysql_error());		
		echo("<table border=1 align='center'>");
		echo("<tr>");
			echo("<td><div align='center'><b>Codice ministeriale</b></div></td><td><div align='center'><b>Descrizione tabella ministeriale</b></div></td><td><div align='center'><b>Descrizione tabella locale</b></div></td>");
		echo("</tr>");
		while($riga=mysql_fetch_row($risultatoMinistero)) // per ogni riga estratta dalla tabella del ministero
		{
			$codiceRiferimento=$riga[0];
			$parentelaRiferimento=$riga[1];

			
			
			//qui, per le parentele della tabella ministeriale che hanno il carattere / nel campo "TipoParentela", estaggo le sottostringhe che ci sono prima  e dopo la barra, così da poter effettuare una query in like sulle parentele che non hanno corrispondenza esatta tra la tabella locale e la tabella ministeriale
			if($codiceRiferimento==2 or $codiceRiferimento==3 or $codiceRiferimento==6 or $codiceRiferimento==7 or $codiceRiferimento==8 or $codiceRiferimento==9 or $codiceRiferimento==11 or $codiceRiferimento==12 or $codiceRiferimento==14 or $codiceRiferimento==15 or $codiceRiferimento==16 or $codiceRiferimento==17 or $codiceRiferimento==18 or $codiceRiferimento==19 or $codiceRiferimento==21 or $codiceRiferimento==28 or $codiceRiferimento==99)
			{
				$query="select substring_index('$parentelaRiferimento','/',-1)
						from ministero
						where id=$codiceRiferimento";
				$risultato=mysql_query($query)
					or die("Impossibile estrarre la parentela sinistra di riferimento: ".mysql_error());
				while($riga=mysql_fetch_row($risultato))
					$parentelaRiferimentoRight=$riga[0];
				$query="select substring_index('$parentelaRiferimento','/',1)
						from ministero
						where id=$codiceRiferimento";
				$risultato=mysql_query($query)
					or die("Impossibile estrarre la parentela destra di riferimento: ".mysql_error());
				while($riga=mysql_fetch_row($risultato))
					$parentelaRiferimentoLeft=$riga[0];
			}
			else
			{
				$parentelaRiferimentoRight='';
				$parentelaRiferimentoLeft='';
			}
			
			
			/*if(strlen($parentelaRiferimentoLeft)>0)
				echo("Parentela riferimento sinistra su tabella ministeriale: ".$parentelaRiferimentoLeft." di lunghezza ".strlen($parentelaRiferimentoLeft)."<br>");
			if(strlen($parentelaRiferimentoRight)>0)
				echo("Parentela riferimento destra su tabella ministeriale: ".$parentelaRiferimentoRight." di lunghezza ".strlen($parentelaRiferimentoRight)."<br>");*/
			
			
			
			$queryLocale="select tipoparentela
						from locale
						where codiceapr4=$codiceRiferimento";
			$risultatoLocale=mysql_query($queryLocale)
				or die("Impossibile effettuare la select sulla tabella 'locale': ".mysql_error());
			while($parentelaLocale=mysql_fetch_row($risultatoLocale))
			{
				if(($parentelaRiferimentoRight!='') and ($parentelaRiferimentoLeft!=''))
				{
					$query="select tipoparentela
							from locale
							where codiceapr4=$codiceRiferimento and (tipoparentela like '%$parentelaRiferimentoLeft%' or tipoparentela like '%$parentelaRiferimentoRight%')";
					$risultato=mysql_query($query)
						or die("Impossibile effettuare query in like per determinare la corrispondenza parziale tra tabella locale e ministero :".mysql_error());
				}
				else
				{
					$query="select tipoparentela
							from locale
							where codiceapr4=$codiceRiferimento";
					$risultato=mysql_query($query)
						or die("Impossibile effettuare query per determinare la corrispondenza tra tabella locale e ministero :".mysql_error());
				}
				$i=0;
				if(mysql_num_rows($risultato)>1)
				{
					while($riga=mysql_fetch_row($risultato))
					{
						$risultatoQuery[$i]=$riga[0];
						//echo("Risultato query in like su tabella locale: ".$risultatoQuery[$i]."<br>");
						$i++;
					}
				}
				elseif(mysql_num_rows($risultato)==1)
				{
					while($riga=mysql_fetch_row($risultato))
					{
						$risultatoQuery[$i]=$riga[0];
						$risultatoQuery[1]='';
					}
				}
				if((strlen($parentelaRiferimentoLeft)>0) and (strlen($parentelaRiferimentoRight)>0))
				{
					if((strcmp($parentelaRiferimento,$parentelaLocale[0])!=0) and ((strpos($risultatoQuery[0],$parentelaRiferimentoLeft)!==false) or (strpos($risultatoQuery[0],$parentelaRiferimentoRight)!==false) or (strpos($risultatoQuery[1],$parentelaRiferimentoLeft)!==false) or (strpos($risultatoQuery[1],$parentelaRiferimentoRight)!==false))) //se la parentela della tabella locale è contenuta nella parentela ministeriale, la riga del risultato sarà dorata
					{
						echo("<tr>");
							echo("<td bgcolor='#FFD700'><div align='center'>".$codiceRiferimento."</div></td>");
							echo("<td bgcolor='#FFD700'><div align='center'>".$parentelaRiferimento."</div></td>");
							echo("<td bgcolor='#FFD700'><div align='center'>".$parentelaLocale[0]."</div></td>");
							//echo("<td bgcolor='#FFD700'><div align='center'>RisultatoQuery[0] (da tabella locale): ".$risultatoQuery[0]."</div></td>"); // ko
							//echo("<td bgcolor='#FFD700'><div align='center'>RisultatoQuery[1]: (da tabella locale) ".$risultatoQuery[1]."</div></td>"); // ko
							//echo("<td bgcolor='#FFD700'><div align='center'>ParentelaRiferimentoLeft: ".$parentelaRiferimentoLeft."</div></td>");
							//echo("<td bgcolor='#FFD700'><div align='center'>ParentelaRiferimentoRight: ".$parentelaRiferimentoRight."</div></td>");
						echo("</tr>");
					}
					elseif(strcmp($parentelaRiferimento,$parentelaLocale[0])==0) // se la descrizione della tabella ministeriale è uguale alla tabella locale, la riga del risultato sarà verde
					{
						echo("<tr>");
							echo("<td bgcolor='#98FB98'><div align='center'>".$codiceRiferimento."</div></td>");
							echo("<td bgcolor='#98FB98'><div align='center'>".$parentelaRiferimento."</div></td>");
							echo("<td bgcolor='#98FB98'><div align='center'>".$parentelaLocale[0]."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>RisultatoQuery[0]: ".$risultatoQuery[0]."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>RisultatoQuery[1]: ".$risultatoQuery[1]."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>ParentelaRiferimentoLeft: ".$parentelaRiferimentoLeft."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>ParentelaRiferimentoRight: ".$parentelaRiferimentoRight."</div></td>");
						echo("</tr>");
					}
					else
					{
						$risultatoQuery[0]='';
						$risultatoQuery[1]='';
						echo("<tr>");
							echo("<td><div align='center'>".$codiceRiferimento."</div></td>");
							echo("<td><div align='center'>".$parentelaRiferimento."</div></td>");
							echo("<td><div align='center'>".$parentelaLocale[0]."</div></td>");
							//echo("<td><div align='center'>RisultatoQuery[0]: ciao ".$risultatoQuery[0]."</div></td>");
							//echo("<td><div align='center'>RisultatoQuery[1]: ciao ".$risultatoQuery[1]."</div></td>");
							//echo("<td><div align='center'>ParentelaRiferimentoLeft: ".$parentelaRiferimentoLeft."</div></td>");
							//echo("<td><div align='center'>ParentelaRiferimentoRight: ".$parentelaRiferimentoRight."</div></td>");
						echo("</tr>");
					}
				}
				else
				{
					if(strcmp($parentelaRiferimento,$parentelaLocale[0])==0) // se la descrizione della tabella ministeriale è uguale alla tabella locale, la riga del risultato sarà verde
					{
						echo("<tr>");
							echo("<td bgcolor='#98FB98'><div align='center'>".$codiceRiferimento."</div></td>");
							echo("<td bgcolor='#98FB98'><div align='center'>".$parentelaRiferimento."</div></td>");
							echo("<td bgcolor='#98FB98'><div align='center'>".$parentelaLocale[0]."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>RisultatoQuery[0]: ".$risultatoQuery[0]."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>RisultatoQuery[1]: ".$risultatoQuery[1]."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>ParentelaRiferimentoLeft: ".$parentelaRiferimentoLeft."</div></td>");
							//echo("<td bgcolor='#98FB98'><div align='center'>ParentelaRiferimentoRight: ".$parentelaRiferimentoRight."</div></td>");
						echo("</tr>");
					}
					elseif((strcmp($parentelaRiferimento,$parentelaLocale[0])!=0) and (strpos($parentelaLocale[0],$parentelaRiferimento)!==false)) //se la parentela della tabella locale è contenuta nella parentela ministeriale, la riga del risultato sarà dorata
					{
						echo("<tr>");
							echo("<td bgcolor='#FFD700'><div align='center'>".$codiceRiferimento."</div></td>");
							echo("<td bgcolor='#FFD700'><div align='center'>".$parentelaRiferimento."</div></td>");
							echo("<td bgcolor='#FFD700'><div align='center'>".$parentelaLocale[0]."</div></td>");
							//echo("<td bgcolor='#FFD700'><div align='center'>RisultatoQuery[0]: ".$risultatoQuery[0]."</div></td>");
							//echo("<td bgcolor='#FFD700'><div align='center'>RisultatoQuery[1]: ".$risultatoQuery[1]."</div></td>");
							//echo("<td bgcolor='#FFD700'><div align='center'>ParentelaRiferimentoLeft: ".$parentelaRiferimentoLeft."</div></td>");
							//echo("<td bgcolor='#FFD700'><div align='center'>ParentelaRiferimentoRight: ".$parentelaRiferimentoRight."</div></td>");
						echo("</tr>");
					}
					else
					{
						echo("<tr>");
							echo("<td><div align='center'>".$codiceRiferimento."</div></td>");
							echo("<td><div align='center'>".$parentelaRiferimento."</div></td>");
							echo("<td><div align='center'>".$parentelaLocale[0]."</div></td>");
							//echo("<td><div align='center'>RisultatoQuery[0]: ".$risultatoQuery[0]."</div></td>"); // ko
							//echo("<td><div align='center'>RisultatoQuery[1]: ".$risultatoQuery[1]."</div></td>"); // ko
							//echo("<td><div align='center'>ParentelaRiferimentoLeft: ".$parentelaRiferimentoLeft."</div></td>");
							//echo("<td><div align='center'>ParentelaRiferimentoRight: ".$parentelaRiferimentoRight."</div></td>");
						echo("</tr>");
					}
				}
			}
		}
		echo("</table>");
		mysql_close($conn);
	?>
	</body>
</html>