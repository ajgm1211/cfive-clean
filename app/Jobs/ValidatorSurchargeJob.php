<?php

namespace App\Jobs;

use App\CalculationTypeContent;
use App\Contract;
use App\LocalCharge;
use App\MasterSurcharge;
use App\Surcharge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PrvSurchargers;

class ValidatorSurchargeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $id = $this->data['id'];
        $contract = Contract::with('carriers', 'direction', 'companyUser', 'direction', 'carriers.carrier', 'gpContainer')->find($id);
        $carrier_contract = $contract->carriers->pluck('carrier_id');
        $contract->result_validator = json_encode([]);
        $contract->validator = false;
        $contract->update();
        $direction_array = null;

        $locals = LocalCharge::with('localcharcarriers.carrier', 'surcharge')->where('contract_id', $id)->get();

        if ($contract->direction_id == 3) {
            $direction_array = [1, 2, 3];
        } else {
            $direction_array = [$contract->direction_id, 3];
        }
        $surcharge_detail = MasterSurcharge::where('group_container_id', $contract->gp_container_id)
            ->orWhere('group_container_id', null)
            ->with('surcharge')
            ->get();

        $surcharge_detail = $surcharge_detail->whereIn('carrier_id', $carrier_contract);
        $surcharge_detail = $surcharge_detail->whereIn('direction_id', $direction_array);

        $local_found_in_sur_mast = collect([]);
        $local_not_found_in_sur_mast = collect([]);
        $carrier_not_content_contract = collect([]);
        $surcharge_not_registred = collect([]);
        $surcharge_duplicated = collect([]);
        $data_final = collect();
        //dd($surcharge_detail,$locals,$contract,$carrier_contract);
        foreach ($locals as $local) {
            $surchargersFined = PrvSurchargers::get_single_surcharger($local->surcharge->name);
            //dd($surchargersFined);
            if ($local->typedestiny_id == 3) {
                $type_destiny_array = [1, 2, 3];
            } else {
                $type_destiny_array = [$local->typedestiny_id, 3];
            }

            if ($surchargersFined['boolean'] == true && $surchargersFined['count'] == 1) {
                $filtered_carrier = $local->localcharcarriers->whereNotIn('carrier_id', $carrier_contract);
                if (count($filtered_carrier) >= 1) {
                    // Agregar la excepcion de que hay un carrier en el local no registrado en el contracto
                    //dd($filtered_carrier->pluck('carrier')->pluck('name')->implode(' | '));
                }

                $master_surcharge_fineds = MasterSurcharge::where('surcharge_id', $surchargersFined['data'])
                    ->where('group_container_id', $contract->gp_container_id)
                    ->orWhere('group_container_id', null)
                    ->get();

                $master_surcharge_fineds = $master_surcharge_fineds->whereIn('carrier_id', $local->localcharcarriers->pluck('carrier_id'));
                $master_surcharge_fineds = $master_surcharge_fineds->whereIn('direction_id', $direction_array);
                $master_surcharge_fineds = $master_surcharge_fineds->whereIn('typedestiny_id', $type_destiny_array);

                //dd($local, $surchargersFined['data'],$master_surcharge_fineds,$contract->direction_id );
                $local_collated = false;
                foreach ($master_surcharge_fineds as $master_surcharge_fined) {
                    if ($master_surcharge_fined->calculationtype_id == $local->calculationtype_id) {
                        //El calculation T. del Reacargo es igual al del Master Surcharge
                        //Agregar a lista exitosa 1
                        $local_collated = true;
                        //dd('//Agregar a lista exitosa 1',$surcharge_detail,$local_collated,$master_surcharge_fined,$local);
                        $local_found_in_sur_mast->push($master_surcharge_fined->id);
                        break;
                    } else {
                        //No es igual el caculation type
                        //dd($master_surcharge_fined,'//No es igual el caculation type');
                        $calculationTypeContent = CalculationTypeContent::where('calculationtype_base_id', $master_surcharge_fined->calculationtype_id)
                            ->where('calculationtype_content_id', $local->calculationtype_id)
                            ->get();
                        if (count($calculationTypeContent) >= 1) {
                            //Agregar a lista exitosa 2
                            $local_collated = true;
                            //dd('//Agregar a lista exitosa 2',$local_collated,$master_surcharge_fined,$local);
                            $local_found_in_sur_mast->push($master_surcharge_fined->id);
                            break;
                        } else {
                            $calculationTypeContent = null;
                            $calculationTypeContent = CalculationTypeContent::where('calculationtype_content_id', $master_surcharge_fined->calculationtype_id)
                                ->where('calculationtype_base_id', $local->calculationtype_id)
                                ->get();
                            if (count($calculationTypeContent) >= 1) {
                                //Agregar a lista exitosa 3
                                $local_collated = true;
                                //dd('//Agregar a lista exitosa 3',$local_collated,$master_surcharge_fined,$local);
                                $local_found_in_sur_mast->push($master_surcharge_fined->id);
                                break;
                            } else {
                                //dd('//No coincide');
                            }
                        }
                    }
                }
                if ($local_collated) {
                    //dd('recargo de este local fue encontrado');
                } else {
                    // informar que no encontro el recargo. agregar a master surchar
                    //dd('recargo de este local no fue encontrado');
                    $local_not_found_in_sur_mast->push($local->surcharge_id);
                }
            } else {
                if ($surchargersFined['count'] == 0) {
                    // No encontro el recargo en variaciones de Surcharge list
                    $surcharge_not_registred->push($local->surcharge_id);
                } elseif ($surchargersFined['count'] >= 1) {
                    // Encontro mas de un Surcharge para una variacion. Listar Error de ID semejantes
                    $surcharge_duplicated->push($surchargersFined['data']);
                }
            }
        }

        // Se listan en Verde
        $surcharMas_locals_found = $surcharge_detail->whereIn('id', $local_found_in_sur_mast->unique());
        // Se pinta en Rojo
        $surcharMas_locals_not_found = $surcharge_detail->whereNotIn('id', $local_found_in_sur_mast->unique());

        //        dd('Surcharge Detaills - Surcharge de Locals encontrados   //  Surcharge_master_id',
        //           $surcharMas_locals_found->pluck('id'),
        //           'Surcharge Detaills - Surcharge de Locals NO encontrados //  Surcharge_master_id',
        //           $surcharMas_locals_not_found->pluck('id'),
        //           'Surcharge de Locals No encontrados - Agregar a Surcharge Detaills //  Surcharge_id',
        //           $local_not_found_in_sur_mast->unique(),
        //           'Surcharge No Registrado en variacion //  surcharge_id',
        //           $surcharge_not_registred->unique(),
        //           'Surcharge Duplicado en variacion //  surcharge_id',
        //           $surcharge_duplicated->unique(),
        //           'Surcharge Master Listado General',
        //           $surcharge_detail->pluck('id')
        //          );
        $array = [];
        $array['surcharMas_locals_found'] = [];
        $array['surcharMas_locals_not_found'] = [];
        $array['local_not_found_in_sur_mast'] = [];
        $array['surcharge_not_registred'] = [];
        $array['surcharge_duplicated'] = [];

        $surcharMas_locals_found->load('direction', 'calculationtype', 'typedestiny');
        foreach ($surcharMas_locals_found as $surcharMas_local_found) {
            //dd($surcharMas_local_not_found);
            array_push($array['surcharMas_locals_found'],
                       $surcharMas_local_found->surcharge->name.' ____ '.
                       $surcharMas_local_found->direction->name.' ____ '.
                       $surcharMas_local_found->calculationtype->name.' ____ '.
                       $surcharMas_local_found->typedestiny->description
                      );
        }
        $surcharMas_locals_not_found->load('direction', 'calculationtype', 'typedestiny');
        foreach ($surcharMas_locals_not_found as $surcharMas_local_not_found) {
            //dd($surcharMas_local_not_found);
            array_push($array['surcharMas_locals_not_found'],
                       $surcharMas_local_not_found->surcharge->name.' ____ '.
                       $surcharMas_local_not_found->direction->name.' ____ '.
                       $surcharMas_local_not_found->calculationtype->name.' ____ '.
                       $surcharMas_local_not_found->typedestiny->description
                      );
        }

        foreach ($local_not_found_in_sur_mast->unique() as $local_surch) {
            $surchar_name = Surcharge::find($local_surch);
            array_push($array['local_not_found_in_sur_mast'], $surchar_name->name);
        }

        foreach ($surcharge_not_registred->unique() as $surch_not_rg) {
            $surchar_name = Surcharge::find($surch_not_rg);
            array_push($array['surcharge_not_registred'], $surchar_name->name);
        }

        foreach ($surcharge_duplicated->unique() as $surch_dp) {
            array_push($array['surcharge_duplicated'], $surch_dp);
        }

        $contract->result_validator = json_encode($array);
        $contract->validator = true;
        $contract->update();
    }
}
