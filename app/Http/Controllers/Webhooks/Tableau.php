<?php

namespace App\Http\Controllers\Webhooks;

use App\Domain;
use App\Field;
use App\Webforms;
use App\Mail\Lead;
use Twilio\Rest\Client;
use \Log;

use Illuminate\Http\Request;

class Tableau extends Base {

	public function process(Request $request) {

		//Log::info(print_r($request->toArray(),1));
		//Log::info(base_path());

		$data = [
			$request->report => []
		];
		$keys = [];

		$row = 0;
		if (($handle = fopen(base_path() . "/tools/" . $request->report . ".csv", "r")) !== FALSE) {
			while (($values = fgetcsv($handle, 20000, ",")) !== FALSE) {
				//Log::info("Row #" . ($row+1) . " (" . count($keys) . " | " . count($values) . ")");
				if($row === 0) {
					$keys = $values;
				} else {
					//$data[$request->report][] = array_combine($keys, $values);
					//Log::info(print_r($values,1));
					try {
						$data[$request->report][] = array_combine($keys, $values);
					} catch (\Exception $e) {}
				}
				$row++;
			}
			fclose($handle);
		}

		//Log::info(print_r($data,1));
		header('Content-Type: application/json');
		echo json_encode($data);
	}

}
