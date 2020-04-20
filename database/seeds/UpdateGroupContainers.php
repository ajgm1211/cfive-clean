<?php

use Illuminate\Database\Seeder;

class UpdateGroupContainers extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('group_containers')->where('id', 1)->update(['code' => 'dry']);
        DB::table('group_containers')->where('id', 2)->update(['code' => 'refeer']);
        DB::table('group_containers')->where('id', 3)->update(['code' => 'opentop']);
        DB::table('group_containers')->where('id', 4)->update(['code' => 'flatrack']);
    }
    
}
