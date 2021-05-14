<?php

use Illuminate\Database\Seeder;

class ApiCarriersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $maersk_variation_array = [];
        $maersk_variation_array['type'] = ["maersk", "maerskfd", "msk", "msk-aruypy", "mskaruypy", "msk-aruypy", "maersk nor"];

        $cma_variation_array = [];
        $cma_variation_array['type'] = ["cma cgm", "cma", "cma cgm nor", "cma-cgm"];

        DB::table('carriers')->insert([
            0 => [
                'id' => 147,
                'name' => 'MAERSK SPOT',
                'uncode' => 'maersk',
                'image' => 'maersk.png',
                'varation' => json_encode($maersk_variation_array)
            ],
            1 => [
                'id' => 148,
                'name' => 'CMA CGM SPOT',
                'uncode' => 'cmacgm',
                'image' => 'cma.png',
                'varation' => json_encode($cma_variation_array)
            ],
        ]);
    }
}
