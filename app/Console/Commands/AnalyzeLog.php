<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyzeLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:analyze-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // ich hab die .log Datei unter storage/analyzer/toBeAnalyzed.log gespeichert
        // mit fopen die Logs öffnen und dann mit fget Zeile für Zeile auslesen und bearbeiten,
        // damit nicht die komplette Datei (>500mb) in den Speicher eingelesen wird
        // https://www.uptimia.com/questions/how-to-read-a-large-file-line-by-line-in-php

        $handle = fopen("storage/analyzer/toBeAnalyzed.log", "r");
        global $serialCount;
        global $serial;
        global $serialToMacs;
        global $sortedSerialToMacs;
        global $sortedSerialCount;
        global $hardwareSpecs;
        global $specsToSerial;
        global $deviceMAC;      //variablen global zur verfügung gestellt, um nach der while schleife damit zu arbeiten

        while (($line = fgets($handle)) !== false) {

            // mit Regex die Serial number und specs extrahieren, erst hab ich das mit explored versucht (weil schneller als Regex) und dann bei jedem Leerzeichen getrennt,
            // hab das aber nicht wirklich zum Laufen bekommen

            if (preg_match('/serial=([A-Z0-9]+)/', $line, $match)) {
                $serial = $match[1];
                if (!isset($serialCount[$serial])) {
                    $serialCount[$serial] = 0;
                }
                $serialCount[$serial]++;
            }

            // hier war ich erst etwas überfordert da ich nicht gesehen hatte das base65 und GZIP in der Anweisung gegeben war danach hatte ich probleme mit gzdecode,
            // habe da immer data error bekommen, mit dem @ werden warnings ignoriert und damit hat das danach einfach funktioniert, ich wei? jetzt ehrlicherweise nicht wieso
            // bzw was der Fehler wart

            if (preg_match('/specs=([a-zA-Z0-9\/+]+)/', $line, $match)) {
                $data = json_decode(@gzdecode(base64_decode($match[1])), true);
                //Das gehört zu Aufgabe 2
                if (isset($data['mac'])) {
                    $deviceMAC = $data['mac'];
                    $serialToMacs[$serial][$deviceMAC] = true;
                }
                //Und das hier zu Aufgabe 3
                //für Aufgabe 3 würde ich genau so vorgehen wie in Aufgabe 2 nur das ich die Serial number der Hardware zu weise
                //da nicht expliziert beschreiben wird wie ich die Hardware Klassifizieren soll werden ich nach Firmwareversion, Arbeitsspeicher, Prozessor und dem Root-Festplattenspeicher gehen
                //da das die Information sind die mich Interessieren

                //In einer echten Anwendung würde ich die 4 Punkte getrennt speichern, damit ich diese dann in der PDF vernünftig und leserlicher Darstellen kann
                //außerdem würde ich RAM und Root-Speicher auf- bzw. abrunden und in GB ausgeben, wodurch ich größer Gruppen bekomme.
                if (isset($data['fwversion']) && isset($data['mem']) && isset($data['cpu']) && isset($data['disk_root'])){
                    $hardwareSpecs = 'Version: ' . $data['fwversion'] . ' RAM: ' . $data['mem'] . ' CPU: ' . $data['cpu'] . ' Root-Speicher: ' . $data['disk_root'];
                    $specsToSerial[$hardwareSpecs][$serial] = true;
                }
            }
        }
        fclose($handle);

        // erst wusste ich nicht wie ich hier weiter machen soll,
        // dachte erst das ich die Serial und die Specs als objekt erzeuge um dann Abhängigkeiten zu erzeugen aber ein ähnliches problem hatte ich schon mal bei einem Project von uns
        // da hab ich aber eine custom Collection erstellt und darüber hab ich dann die collect() gefunden, sehr geiles Ding um damit die top 10 herauszuholen

        $sortedSerialCount = collect($serialCount)
            ->sortDesc()
            ->take(10);
//        var_dump($sortedSerialCount);
        $sortedSerialToMacs = collect($serialToMacs)
            ->sortDesc()
            ->take(10);
//        var_dump($sortedSerialToMacs);
        $sortedSpecsToSerial = collect($specsToSerial)
            ->sortDesc()
            ->take(10);
//        var_dump($sortedSpecsToSerial);


        //hier würde ich jetzt die aufforderung einauen die die information in einer PDF speichert, dazu würde ich dann eine view.blade.php erstellen die den Content des PDFs erzeugt
        //ich hab mich für das https://github.com/barryvdh/laravel-dompdf package entschieden, gab ein kurzes Youtube tutorial wo ich das gefunden habe, sehr easy zu Nutzen

        $pdf = Pdf::loadView('pdf', [
            'sortedSerialCount' => $sortedSerialCount,
            'sortedSerialToMacs' => $sortedSerialToMacs,
            'sortedSpecsToSerial' => $sortedSpecsToSerial
        ]);

        $pdf->save('storage/analyzer/analyzedLog.pdf');
        return 0;
    }

}
