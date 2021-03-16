/*

per importarlo è necessario accedere al monitor MySql dal prompt dei comandi e digitare
source [percorso file con barre \ + nome_file --> es. C\documenti\creazionedb.sql]

variabile d'ambiente C:\Program Files (x86)\EasyPHP 2.0b1\mysql\bin\
 
*/

create database confronto_parentele;

use confronto_parentele;

create table ministero /* contiene i dati della tabella ministeriale */
(
id tinyint(2),
TipoParentela varchar(35),
PRIMARY KEY(id)
);

create table locale /* tabella dove verrà importata in csv la tabella "parent" del database di produzione del Cliente */
(
Sigla varchar(2),
TipoParentela varchar(35),
CodiceAPR4 tinyint(2),
OrdineLista tinyint(2),
TipoSesso char(1),
PRIMARY KEY(Sigla)
);

create table cliente /* tabella dove verrà inserito il nome del Cliente per cui si sta eseguendo il confronto */
(
cliente varchar(60),
primary key(cliente)
);


/* qui inserisco nella tabella ministero i valori presenti nell'excel scaricato dal sito di ANPR */
insert into ministero values(1,'INTESTATARIO SCHEDA');
insert into ministero values(2,'MARITO/MOGLIE');
insert into ministero values(3,'FIGLIO/FIGLIA');
insert into ministero values(4,'NIPOTE');
insert into ministero values(5,'PRONIPOTE');
insert into ministero values(6,'PADRE/MADRE');
insert into ministero values(7,'NONNO/NONNA');
insert into ministero values(8,'BISNONNO/BISNONNA');
insert into ministero values(9,'FRATELLO/SORELLA');
insert into ministero values(10,'NIPOTE');
insert into ministero values(11,'ZIO/ZIA');
insert into ministero values(12,'CUGINO/CUGINA');
insert into ministero values(13,'ALTRO');
insert into ministero values(14,'FIGLIASTRO/FIGLIASTRA');
insert into ministero values(15,'PATRIGNO/MATRIGNA');
insert into ministero values(16,'GENERO/NUORA');
insert into ministero values(17,'SUOCERO/SUOCERA');
insert into ministero values(18,'COGNATO/COGNATA');
insert into ministero values(19,'FRATELLASTRO/SORELLASTRA');
insert into ministero values(20,'NIPOTE');
insert into ministero values(21,'ZIO/ZIA');
insert into ministero values(22,'ALTRO');
insert into ministero values(23,'CONVIVENTE');
insert into ministero values(24,'RESPONSABILE DELLA CONVIVENZA');
insert into ministero values(25,'CONVIVENTE');
insert into ministero values(26,'TUTORE');
insert into ministero values(28,'UNITO CIVILMENTE/UNITA CIVILMENTE');
insert into ministero values(80,'ADOTTATO');
insert into ministero values(81,'NIPOTE');
insert into ministero values(99,'NON DEFINITO/NON COMUNICATO');

insert into cliente values('');