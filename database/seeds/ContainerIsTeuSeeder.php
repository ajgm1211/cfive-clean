<?php

use Illuminate\Database\Seeder;

class ContainerIsTeuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $containers = DB::table('containers')->get();
        $non_teu_containers = DB::table('containers')->where('code', 'like', '20%')->pluck('code')->toArray();

        foreach($containers as $container){
            $options = json_decode($container->options,true);

            if(in_array($container->code,$non_teu_containers)){
                $options['is_teu'] = false; 
            }else{
                $options['is_teu'] = true;
            }

            $options_json = json_encode($options);
            DB::table('containers')
                ->where('id', $container->id)
                ->update(['options' => $options_json]);
        }
    }
}
