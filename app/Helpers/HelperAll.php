<?php
//app/Helpers/Envato/User.php
namespace App\Helpers;

use App\Rate;

class HelperAll {
	/**
     * @param int $user_id User-id
     * 
     * @return string
     */
	public static function addOptionSelect($dataAll,$id,$name) {
		$data	= [null=>'Please Select'];
		foreach($dataAll as $dataRun){
			$data[$dataRun[$id]] = $dataRun[$name];
		}
		return $data;
	}
}