# Probeaufgabe für SP

## Allgemeine Infos
Erstmal bedanke ich mich für die Möglichkeit, mein Können unter Beweis zu stellen.  
Es war das erste Mal, dass ich ein Analyse-Tool für Logdateien geschrieben habe, war aber erstaunt, wie gut das am Ende doch geklappt hat.  
Mir ist dabei aufgefallen, dass es für mich schwer war, während des Arbeitens meine Gedanken aufzuschreiben, deswegen habe ich das immer erst  
nachdem ich fertig war, gemacht. Dadurch habe ich ein bisschen das Gefühl, dass ich den Hauptgrund der Aufgabe verfehlt habe,  
aber ich habe versucht, so gut es geht, meine Gedanken im Nachhinein zu dokumentieren.

## Aufgabe 1
**Umsetzung in:** `app/Console/Commands/AnalyzeLog.php`
## Vorgehen
Ich habe mit Regex die Seriennummern extrahiert und in einem Array gezählt (`serial => count`).  
Diese Daten habe ich sortiert und als Collection an die Blade-View übergeben, um sie in der PDF darzustellen.
### Gedanken:
War relativ einfach, da ich nur eine Zahlen- und Buchstabenfolge filtern und die Häufigkeit zählen musste.  
Das habe ich dann in einem Array gespeichert und an die Blade-Datei übergeben, damit die PDF erstellt werden kann.

## Aufgabe 2
**Umsetzung in:** `app/Console/Commands/AnalyzeLog.php`
## Vorgehen
Um herauszufinden, ob eine Serial Number nur von einem Gerät benutzt wird, habe ich einfach bei jedem Zugriff die MAC-Adresse  
mit der Serial Number in einem Array verknüpft. Wenn eine Serial Number bereits eine MAC-Adresse hatte, wurde die neue MAC-Adresse  
einfach ins Array hinzugefügt. So kann ich dann die MAC-Adressen zählen, um zu sehen, auf wie vielen Geräten die Serial Number  
installiert ist.  
### Gedanken:
Hier hatte ich schon mehr Probleme, die Specs aus der Logdatei zu extrahieren war nicht schwer, ich musste nur herausfinden,  
wie diese verschlüsselt waren. Das habe ich dann nach gründlichem Lesen des Aufgabenblattes herausgefunden. Danach hatte  
ich dann ein Problem mit `gzdecode`, da ich dort immer wieder einen Data-Error-Fehler bekommen habe. Das muss damit zusammenhängen,  
dass die decoden nicht immer erfolgreich war und dort fehlerhafte GZIPs herauskamen.  
Durch das `@` werden diese Warnungen aber ignoriert, dadurch werden eventuell auch die Ergebnisse verfälscht, da ich meine  
MAC-Adressen so nicht extrahieren konnte. Dazu kommt noch, dass teilweise keine MAC-Adressen eingetragen waren,  
das habe ich nicht verstanden, wie kann das sein? Auf jeden Fall fehlen dadurch einige Einträge im Ergebnis.  
Das habe ich dann in einem Array gespeichert und an die Blade-Datei übergeben, damit die PDF erstellt werden kann.



## Versionen
- Laravel 11
- node: 22.x.x
- php 8.4

## Lokal aufsetzen
- `composer install` composer package installieren
- `.env` Datei anlegen (zB mit `cp .env.example .env`)
- `php artisan key:generate` im Root Ordner ausführen
- `npm ci` npm installieren
- Log Datei in `/storage/analyzer/` speichern und in `toBeAnalyzed.log` umbenennen
- mit dem Befehl `php artisan app:analyze-log` den analyse Prozess einleiten
- danach entsteht in `/storage/analyzer/` ein PDF mit dem namen `analyzedLog.pdf`

## Quelldaten
`resources`
`storage`
