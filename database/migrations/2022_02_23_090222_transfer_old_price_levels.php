<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Price;
use App\PriceLevel;
use App\PriceLevelDetail;
use App\PriceLevelGroup;

class TransferOldPriceLevels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //retrieving old price levels with relations
        $old_price_levels = Price::with('company_name','freight_markup','local_markup','inland_markup')->get();

        //adapting to new structure
        foreach($old_price_levels as $old_price) {
            $new_structure = [];
            
            foreach($old_price->freight_markup as $freight_markup){
                $apply = 1;
                
                //Checking freight type-> FCL OR LCL
                if($freight_markup->price_type_id == 1) {
                    $type = 'FCL';
                }elseif($freight_markup->price_type_id == 2) {
                    $type = 'LCL';
                }else{
                    continue;
                }
                
                if(!isset($new_structure[$type])){
                    $new_structure[$type] = [];
                }

                //Checking freight apply-> % or fix
                if($freight_markup->fixed_markup > 0) {
                    $amount = $freight_markup->fixed_markup;
                    $amount_type = 'Fixed Markup';
                    //storing currency
                    $currency = $freight_markup->currency;
                }elseif($freight_markup->percent_markup > 0) {
                    $amount = $freight_markup->percent_markup;
                    $amount_type = 'Percent Markup';
                    $currency = null;
                }else{
                    continue;
                }
    
                if($type == 'FCL') {
                    $amount = [
                        'type_20' => [
                            'amount' => $amount,
                            'markup' => $amount_type
                        ],
                        'type_40' => [
                            'amount' => $amount,
                            'markup' => $amount_type
                        ]
                    ];
                }elseif($type == 'LCL') {
                    $amount = [
                        'type_lcl' => [
                            'amount' => $amount,
                            'markup' => $amount_type
                        ]
                    ];
                }

                //setting up new structure
                $new_price = compact('apply','type','currency','amount');
                $new_structure[$type]['freight'] = $new_price;
                
            } 
            
            foreach($old_price->local_markup as $local_markup){
                $apply = 2;
                
                //Checking local type-> FCL OR LCL
                if($local_markup->price_type_id == 1) {
                    $type = 'FCL';
                }elseif($local_markup->price_type_id == 2) {
                    $type = 'LCL';
                }else{
                    continue;
                }
                
                if(!isset($new_structure[$type])){
                    $new_structure[$type] = [];
                }

                //Checking local apply-> % or fix
                if($local_markup->fixed_markup_import > 0 || $local_markup->fixed_markup_export > 0) {
                    $amount_import = $local_markup->fixed_markup_import;
                    $amount_type_import = 'Fixed Markup';

                    $currency_import = $local_markup->currency_import;

                    $amount_export = $local_markup->fixed_markup_export;
                    $amount_type_export = 'Fixed Markup';

                    //storing currency
                    $currency_export = $local_markup->currency_export;
                }elseif($local_markup->percent_markup_import > 0 || $local_markup->percent_markup_export > 0) {
                    $amount_import = $local_markup->percent_markup_import;
                    $amount_type_import = 'Percent Markup';

                    $currency_import = null;

                    $amount_export = $local_markup->percent_markup_export;
                    $amount_type_export = 'Percent Markup';

                    $currency_export = null;
                }else{
                    continue;
                }

                if($type == 'FCL') {
                    $amount_import = [
                        'type_20' => [
                            'amount' => $amount_import,
                            'markup' => $amount_type_import
                        ],
                        'type_40' => [
                            'amount' => $amount_import,
                            'markup' => $amount_type_import
                        ]
                    ];

                    $amount_export = [
                        'type_20' => [
                            'amount' => $amount_export,
                            'markup' => $amount_type_export
                        ],
                        'type_40' => [
                            'amount' => $amount_export,
                            'markup' => $amount_type_export
                        ]
                    ];
                }elseif($type == 'LCL') {
                    $amount_import = [
                        'type_lcl' => [
                            'amount' => $amount_import,
                            'markup' => $amount_type_import
                        ]
                    ];

                    $amount_export = [
                        'type_lcl' => [
                            'amount' => $amount_export,
                            'markup' => $amount_type_export
                        ]
                    ];
                }

                //setting up new structure
                $new_price_import = compact('apply','type','currency_import','amount_import');
                $new_price_export = compact('apply','type','currency_export','amount_export');

                if(!isset($new_structure[$type]['local'])){
                    $new_structure[$type]['local'] = [];
                }

                $new_structure[$type]['local']['import'] = $new_price_import;
                $new_structure[$type]['local']['export'] = $new_price_export;
            } 
            
            foreach($old_price->inland_markup as $inland_markup){
                $apply = 3;
                
                //Checking local type-> FCL OR LCL
                if($inland_markup->price_type_id == 1) {
                    $type = 'FCL';
                }elseif($inland_markup->price_type_id == 2) {
                    $type = 'LCL';
                }else{
                    continue;
                }
                
                if(!isset($new_structure[$type])){
                    $new_structure[$type] = [];
                }

                //Checking local apply-> % or fix
                if($inland_markup->fixed_markup_import > 0 || $inland_markup->fixed_markup_export > 0) {
                    $amount_import = $inland_markup->fixed_markup_import;
                    $amount_type_import = 'Fixed Markup';

                    $currency_import = $inland_markup->currency_import;

                    $amount_export = $inland_markup->fixed_markup_export;
                    $amount_type_export = 'Fixed Markup';

                    //storing currency
                    $currency_export = $inland_markup->currency_export;
                }elseif($inland_markup->percent_markup_import > 0 || $inland_markup->percent_markup_export > 0) {
                    $amount_import = $inland_markup->percent_markup_import;
                    $amount_type_import = 'Percent Markup';

                    $currency_import = null;

                    $amount_export = $inland_markup->percent_markup_export;
                    $amount_type_export = 'Percent Markup';

                    $currency_export = null;
                }else{
                    continue;
                }

                if($type == 'FCL') {
                    $amount_import = [
                        'type_20' => [
                            'amount' => $amount_import,
                            'markup' => $amount_type_import
                        ],
                        'type_40' => [
                            'amount' => $amount_import,
                            'markup' => $amount_type_import
                        ]
                       
                    ];

                    $amount_export = [
                        'type_20' => [
                            'amount' => $amount_export,
                            'markup' => $amount_type_export
                        ],
                        'type_40' => [
                            'amount' => $amount_export,
                            'markup' => $amount_type_export
                        ]
                    ];
                }elseif($type == 'LCL') {
                    $amount_import = [
                        'type_lcl' => [
                            'amount' => $amount_import,
                            'markup' => $amount_type_import
                        ]
                    ];

                    $amount_export = [
                        'type_lcl' => [
                            'amount' => $amount_export,
                            'markup' => $amount_type_export
                        ]
                    ];
                }

                //setting up new structure
                $new_price_import = compact('apply','type','currency_import','amount_import');
                $new_price_export = compact('apply','type','currency_export','amount_export');
                
                if(!isset($new_structure[$type]['inland'])){
                    $new_structure[$type]['inland'] = [];
                }

                $new_structure[$type]['inland']['import'] = $new_price_import;
                $new_structure[$type]['inland']['export'] = $new_price_export;
            } 

            if(!empty($new_structure)) {
                foreach($new_structure as $type => $new_data){
                    $new_price_level = PriceLevel::create([
                        'name' => $old_price->name,
                        'display_name' => $old_price->name . ' ' . $type,
                        'description' => $old_price->description,
                        'options' => ['whitelabel' => false],
                        'type' => $type,
                        'company_user_id' => $old_price->company_user_id
                    ]);

                    foreach($old_price->company_name as $company){
                        $new_price_level_group = PriceLevelGroup::create([
                            'group_id' => $company->id,
                            'group_type' => 'App\\Company',
                            'price_level_id' => $new_price_level->id,
                        ]);
                    }

                    foreach($new_data as $applies => $price_data){
                        if($applies == 'freight'){                            
                            $new_detail = PriceLevelDetail::create([
                                'amount' => $price_data['amount'],
                                'price_level_id' => $new_price_level->id,
                                'currency_id' => $price_data['currency'],
                                'direction_id' => 3,
                                'price_level_apply_id' => $price_data['apply']
                                            
                            ]);
                        }elseif($applies == 'local' || $applies == 'inland'){
                            foreach($price_data as $direction => $direction_data){
                                $new_detail = PriceLevelDetail::create([
                                    'amount' => $direction_data['amount_'.$direction],
                                    'price_level_id' => $new_price_level->id,
                                    'currency_id' => $direction_data['currency_'.$direction],
                                    'direction_id' => $direction == 'export' ? 2 : 1,
                                    'price_level_apply_id' => $direction_data['apply']
                                                
                                ]);
                            }
                        }

                    }
                }
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
