<?php

namespace App\Console\Commands;

use App\Duplicados;
use App\Harbor;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class JoinVariationHarbor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:variation_harbors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            try
            {
                $filename = public_path().'/duplicados.csv';

                Excel::load($filename, function ($reader) {
                    $reader->each(function ($sheet) {
                        // recorre las filas
                        $sheet->each(function ($row) {
                            $col = explode(';', $row);

                            $original = $col[0];

                            $duplicado = explode(' ', $col[1]);

                            $arrayduplicados = array();
                            $arreglo = array();
                            $arrayFinal = array();
                            $variacionesFinal = array();
                            $variationDupli = array();
                            $variacionesDupli = array();

                            $arrayduplicados2 = array();
                            $arreglo2 = array();
                            $arrayFinal2 = array();
                            $variacionesFinal2 = array();
                            $variationDupli2 = array();
                            $variacionesDupli2 = array();

                            $arrayFin['type'] = array();
                            $duplicados = new Duplicados;
                            $duplicados->id_original = $original;

                            $variationOrig = Harbor::where('id', $original)->first();
                            $variacionesOrig = array(json_decode($variationOrig->varation));

                            if (count($duplicado) > 1) {

                                foreach ($duplicado as $dupli) {

                                    $arrayduplicados[] = $dupli;

                                    $variationDupli = Harbor::where('id', $dupli)->first();
                                    $variacionesDupli = array(json_decode($variationDupli->varation));
                                    // dd($variacionesOrig[0]);
                                    $arreglo = array_merge($variacionesOrig[0]->type, $variacionesDupli[0]->type);
                                    $arrayFinal = array_merge($arrayFinal, $arreglo);

                                }

                                
                                $arrayFin['type'] = array_keys(array_flip($arrayFinal));
                                //     dd($arrayFin['type']);
                                $variacionesFinal = json_encode($arrayFin, JSON_UNESCAPED_UNICODE);

                                $duplicados->duplicados = json_encode($arrayduplicados);
                                $duplicados->varation = $variacionesFinal;
                                $duplicados->save();
                            } else {

                                $variationDupli2 = Harbor::where('id', $duplicado[0])->first();
                                $variacionesDupli2 = array(json_decode($variationDupli2->varation));

                                $arreglo2 = array_merge($variacionesOrig[0]->type, $variacionesDupli2[0]->type);
                                $arrayFinal2 = array_merge($arrayFinal2, $arreglo2);

                                $valoresUnicos = array_keys(array_flip($arrayFinal2));

                                $arrayFin2['type'] = $valoresUnicos;

                                $variacionesFinal2 = json_encode($arrayFin2, JSON_UNESCAPED_UNICODE);
                                $dupli[] = $duplicado[0];
                                $duplicados->duplicados = json_encode($dupli);
                                $duplicados->varation = $variacionesFinal2;
                                $duplicados->save();

                            }
                        });
                    });
                });

            } catch (Illuminate\Filesystem\FileNotFoundException $exception) {
                die("No existe el archivo");
            }

        } catch (\Exception $e) {
            return $this->info($e->getMessage());
        }
        $this->info('Command to join variation on harbors duplicated is succesfull ');
    }
}
