<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

//        https://www.uptimia.com/questions/how-to-read-a-large-file-line-by-line-in-php

        $handle = fopen("storage/analyzer/updatev12-access-pseudonymized.log", "r");
        $count = 0;

        while (($line = fgets($handle)) !== false && $count <= 10) {
//            $parts = explode(' ', $line);
//
//            foreach ($parts as $part) {
//                if (str_starts_with($part, 'serial=')) {
//                    $serial = substr($part, 7);
//                    echo $serial;
//                }
//
//                if (str_starts_with($part, 'specs=')) {
//                    $specs = substr($part, 6);
//                    echo $specs;
//                }
//
//            }

            preg_match('/serial=([A-F0-9]+)/', $line, $serialMatch);
            $serial = $serialMatch[1] ?? null;


            preg_match('/specs=([A-Za-z0-9+\/=]+)/', $line, $specsMatch);
            $data = json_decode(gzdecode(base64_decode($specsMatch[1])), true);
            $deviceMAC = $data['mac'];
            var_dump($deviceMAC);



            $count++;
        }
        fclose($handle);

    }
}
