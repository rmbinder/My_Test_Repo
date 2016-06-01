# UPDATE.md

Diese Datei gibt ergänzende Hinweise bei einen Update einer bestehenden Version.

## Sicherung

Es wird **DRINGEND** empfohlen, vor jedem Update eine Sicherung anzulegen.

## Hinweise

1. ##### Updates vor Version 3.3.7 (Admidio 2.4)

  Vor Version 3.3.7 setzte jedes Update auf die vorhergehende Version auf. I.d.R. musste jedes Update installiert werden.

2. ##### Updates ab Version 3.3.7

  Ab Version 3.3.7 werden notwendige Änderungen beim Überspringen einer Version nachinstalliert.

3. ##### Update auf Version 4.1.0

  Ab 1. Februar 2016 gibt es keine Kontonummern mehr.
  Die Verarbeitung von Kontonummern und Bankleitzahlen durch das Plugin wird deshalb eingestellt. In der Datenbank vorhandene Kontonummern und Bankleitzahlen werden jedoch **NICHT** automatisch gelöscht.

  Sollen alle Kontonummern und Bankleitzahlen automatisch gelöscht werden, so ist **VOR** dem Hochladen der PHP-Dateien auf den Server folgendes durchzuführen:

  In der Datei configtable.php aus dem Ordner classes
  sind die Zeilen 114 und 115
  
  `//$this->delete_member_data(3,'KONTONUMMER');`
  
  `//$this->delete_member_data(3,'BANKLEITZAHL');`
  
  abzuändern in:
  
  `$this->delete_member_data(3,'KONTONUMMER');`
  
  `$this->delete_member_data(3,'BANKLEITZAHL');`
      
  Dadurch werden beim ersten Start des Plugins alle Kontonummern, alle Bankleitzahlen und auch die dazugehörigen Profilfelder gelöscht.

4. ##### Update auf Version 4.1.2
 
  Während des Updates auf diese Version werden umfangreiche Änderungen an der Tabellenstruktur durchgeführt.
  
  **! ! ! Es wird DRINGEND empfohlen, vor dem Update eine Sicherung anzulegen. ! ! !**


