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
        $carrierSealand = Carrier::where('name', 'Sealand')->first();

        $varationArray = json_decode($carrierSealand->varation, true);

        $type = $varationArray['type'];
        array_push($type, $newCode);
        $varationArray['type'] = $type;

        $carrierSealand->update(['varation' => json_encode($varationArray)]);
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
