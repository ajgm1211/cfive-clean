<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Carrier;

class UpdateVarationsToCarriers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $newCode = 'seau'; // new code Sealand Americas 

        $varationArray = json_decode(Carrier::find(24)->varation, true);

        $type = $varationArray['type'];
        array_push($type, $newCode);
        $varationArray['type'] = $type;

        Carrier::find(24)->update(['varation' => json_encode($varationArray)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carriers', function (Blueprint $table) {
            //
        });
    }
}
