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
                if (isset($data['mac'])) {
                    $deviceMAC = $data['mac'];
                }
                $serialToMacs[$serial][$deviceMAC] = true;
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


        //hier würde ich jetzt die aufforderung einauen die die information in einer PDF speichert, dazu würde ich dann eine view.blade.php erstellen die den Content des PDFs erzeugt
        //ich hab mich für das https://github.com/barryvdh/laravel-dompdf package entschieden, gab ein kurzes Youtube tutorial wo ich das gefunden habe, sehr easy zu Nutzen

        $pdf = Pdf::loadView('pdf', [
            'sortedSerialCount' => $sortedSerialCount,
            'sortedSerialToMacs' => $sortedSerialToMacs,
        ]);

        $pdf->save('storage/analyzer/analyzedLog.pdf');
        return 0;
    }

}
