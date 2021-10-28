<?php
$rechnungs_nummer=$_POST["rnr"];

$host = "localhost";
$user = "root";
$password = "";
$db = "rechnungsprojekt";

$verbindung = mysqli_connect($host,$user,$password,$db) or die("Verbindungsfehler!");

// Welche Produkte (Posten) gehören zur Rechnung?
$abfrage = "SELECT p.bezeichnung, k.menge, p.preis, k.knr
            FROM kauft k, produkt p
            WHERE k.pid=p.pid
              AND rnr=$rechnungs_nummer";
$ergebnis = mysqli_query($verbindung, $abfrage) or die("Abfragefehler: Rechnungsposten");
$rechnungs_posten = [];
while ($zeile=mysqli_fetch_array($ergebnis)) {
  $rechnungs_posten[] = [$zeile[0], $zeile[1], $zeile[2]];
  $knr = $zeile[3];
}
if (!isset($rechnungs_posten[0])) die("Rechnung nicht gefunden!");

// An welche Adresse geht die Rechnung?
$abfrage = "SELECT k.*, o.name
            FROM kunde k, ort o
            WHERE k.plz=o.plz
              AND knr=$knr";
$ergebnis = mysqli_query($verbindung, $abfrage) or die("Abfragefehler: Adresse");
$adresse = mysqli_fetch_array($ergebnis);

// Wie lautet das Rechnungsdatum?
$abfrage = "SELECT DATE_FORMAT(datum, '%d.%m.%Y')
            FROM rechnung
            WHERE rnr=$rechnungs_nummer";
$ergebnis = mysqli_query($verbindung, $abfrage) or die("Abfragefehler: Rechnungsnummer");
$rechnungs_datum = mysqli_fetch_array($ergebnis)[0];


$rechnungs_header = '
<img src="rechnung-logo.png" width=100em>
<b>Hard- und Software-Handels GmbH</b>
Hautstraße 67
99999 Niergendwo

Telefon: 099/1231 12 - Telefax: 099/1231 13';

$rechnungs_empfaenger = "$adresse[2] $adresse[3]
$adresse[4]
$adresse[5] $adresse[6]";

$rechnungs_footer = "<hr>
<b>Hard- und Software-Handels GmbH</b>

Geschäftszeiten: Montag bis Freitag 08:00 bis 18:00 Uhr, Samstags 08:00 bis 16 Uhr
Bankverbindung: Kreissparkasse Niergendwo, IBAN DE39 9999 0045 0000 0047 11
Sitz: Niergendwo, Amsgericht: Niergendwo, HRB 1234";

//////////////////////////// Rechnung als HTML-Code \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
$html = '
<table cellpadding="5" cellspacing="0" style="width: 100%; ">
 <tr>
   <td>'.nl2br(trim($rechnungs_header)).'</td>
   <td style="text-align: right">
     Kundennummer:    '.$knr.'<br>
     Rechnungsnummer: '.$rechnungs_nummer.'<br>
     Rechnungsdatum:  '.$rechnungs_datum.'<br>
   </td>
 </tr>

  <tr>
    <td style="font-size:1.3em; font-weight: bold;">
      <br><br>
      Rechnung
      <br>
    </td>
  </tr>

  <tr>
    <td colspan="2">'.nl2br(trim($rechnungs_empfaenger)).'</td>
  </tr>
</table>
<br><br><br>

<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">
  <tr style="background-color: #cccccc; padding:5px;">
    <td style="padding:5px;"><b>Bezeichnung</b></td>
    <td style="text-align: center;"><b>Menge</b></td>
    <td style="text-align: right;"><b>Einzelpreis</b></td>
    <td style="text-align: right;"><b>Preis</b></td>
  </tr>';

$gesamtpreis = 0;

// Auflistung der verschiedenen Posten im Format [Produktbezeichnung, Menge, Einzelpreis]
foreach($rechnungs_posten as $posten) {
  $menge = $posten[1];
  $einzelpreis = $posten[2];
  $preis = $menge*$einzelpreis;
  $gesamtpreis += $preis;
  $html .= '
  <tr>
    <td>'.$posten[0].'</td>
    <td style="text-align: center;">'.$posten[1].'</td>
    <td style="text-align: right;">'.number_format($posten[2], 2, ',', '').' Euro</td>
    <td style="text-align: right;">'.number_format($preis, 2, ',', '').' Euro</td>
  </tr>';
}
$html .="</table>";

$html .= '
<hr>
<table cellpadding="5" cellspacing="0" style="width: 100%;" border="0">';

$html .='
  <tr>
    <td colspan="3"><b>Endbetrag: </b></td>
    <td style="text-align: right;"><b>'.number_format($gesamtpreis, 2, ',', '').' Euro</b></td>
  </tr>
</table>
<br><br><br>';

$html .= nl2br($rechnungs_footer);

echo $html;

mysqli_close($verbindung);
mysqli_free_result($ergebnis);
?>
