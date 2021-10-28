<?php
$vname=$_POST["kvname"];
$nname=$_POST["knname"];
$str=$_POST["kstr"];
$plz=$_POST["kplz"];
$ort=$_POST["kort"];

if ($nname=="") die("Name fehlt!");
if ($vname=="") $vname="%";
if ($str=="") $str="%";
if ($plz=="") $plz="%";
if ($ort=="") $ort="%";


$host = "localhost";
$user = "root";
$password = "";
$db = "rechnungsprojekt";

$verbindung = mysqli_connect($host,$user,$password,$db) or die("Verbindungsfehler!");

$abfrage = "select knr
            from kunde k, ort o
            where k.plz=o.plz
              and nachname like '$nname'
              and vorname like '$vname'
              and k.plz like '$plz'
              and strasse like '$str'
              and o.name like '$ort'";

$ergebnis = mysqli_query($verbindung, $abfrage) or die("Abfragefehler!");

while($zeile=mysqli_fetch_row($ergebnis)) {
  $knr=$zeile[0];
  echo "Kundennummer: ", $knr, "<br>";
  echo $vname, " ", $nname, " ", $str, " ", $plz, " ", $ort, "<br><br>";
}

if (!isset($knr)) echo "Keine Kundennummer gefunden!";

mysqli_close($verbindung);
mysqli_free_result($ergebnis);
?>
