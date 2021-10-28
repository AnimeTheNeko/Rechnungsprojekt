<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>
    <?php
      $knr=$_POST["knr"];
      if ($knr == "") die("Keine kunden nummer eingegeben");

      $host = "localhost";
      $user = "root";
      $password = "";
      $db = "rechnungsprojekt";
      $verbindung = mysqli_connect($host,$user,$password,$db) or die("Verbindungsfehler!");

      $abfrage = "select * from kunde where knr = '$knr'";

      $ergebnis = mysqli_query($verbindung, $abfrage) or die("Abfragefehler!");
      $daten[] = $ergebnis;



     ?>
  </body>
</html>
