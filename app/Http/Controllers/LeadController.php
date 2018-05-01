<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Lead;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;

use App\Jobs\postReserveInteractiveLead;

class LeadController extends Controller
{
    /**
     * Takes a request object and a source string and saves the raw lead information. Returns the lead object.
     *
     * @param $request object
     * @param $source string
     * @return Lead object
     */
    protected function _saveRawToLeadsTable($request, $source, $table)
    {
        $lead = new Lead();
        $lead->source = $source;
        $lead->table = $table;
        $lead->raw = serialize($request->all());
        $lead->save();
        return $lead;
    }

    protected function _publish($publish_to)
    {
        // todo: add try/catch to this loop
        foreach ($publish_to as $key => $value) {
            $func = '_publishTo' . $key;
            $this->$func($value);
        }
    }

    protected function _publishToReserveInteractive($v)
    {
        $requestName = 'MemberLeadImport';

        if (isset($v['form_data']['lead_type'])) {
            if ($v['form_data']['lead_type']  == 'event')
            $requestName = 'EventLeadImport';
        }
	    try {
		    $this->dispatch(new postReserveInteractiveLead($requestName, $v['json'], $v['lead']->id));
	    } catch (\Exception $e) {
		    $u = \App\User::find(1);
		    $u->notify(new \App\Notifications\ApiError(json_decode($e->getMessage())));
		    abort(500, $e->getMessage());
	    }
    }

	/**
	 * Takes form data and cleans it up, then, adds it to the appropriate places for the unbounce
	 * data model object.
	 *
	 * @param $form_data
	 */
	protected function _normalizeInputAndAddToUnbounceObject($form_data)
	{
		$sites = \Config::get("ri.sites");

		$data = [];
		foreach ($form_data as $k => $curr_data) {
			$data[strtolower($k)] = (is_array($curr_data) ? $curr_data[0] : $curr_data);
		}

		if(!isset($data['lead_type']) || !in_array(strtolower($data['lead_type']), ["member","event"])) {
			$messageClass            = new class {};
			$messageClass->status    = "ERROR";
			$messageClass->message   = "Invalid or missing Lead Type" . (isset($data['lead_type']) ? " ({$data['lead_type']})" : "") . ". Valid Lead Types are: Member or Event";
			$messageClass->form_data = $data;
			$u = \App\User::find(1);
			$u->notify(new \App\Notifications\ApiError($messageClass));
			throw new \Exception("Invalid or missing Lead Type" . (isset($data['lead_type']) ? " ({$data['lead_type']})" : "") . ". Valid Lead Types are: Member or Event");
		}

		$site_field = isset($data['site']) ? "site" : "club";

		switch(true) {
			case in_array($data[$site_field], $sites):
				break;
			case count($matches = preg_grep("/{$data[$site_field]}/i", $sites)):
				$matches = array_values($matches);
				$data[$site_field] = $matches[0];
				break;
			case isset($sites[$data[$site_field]]):
				$data[$site_field] = $sites[$data[$site_field]];
				break;
			default:
				// No valid Site/Club found.
				$found = false;
				$original_site = $data[$site_field];
				$data[$site_field] = trim(preg_replace("/(The|Country|Club|Golf|Course)/i", "", $data[$site_field]));
				foreach($sites as $short_code => $site_name) {
					if(preg_match("/{$data[$site_field]}/", $site_name)) {
						Log::info("FOUND A MATCHING SITE!!!!!");
						$data[$site_field] = $site_name;
						$found = true;
						break;
					}
				}
				if(!$found) {
					$messageClass            = new class {};
					$messageClass->status    = "ERROR";
					$messageClass->message   = "Invalid or missing Site/Club Name ({$original_site})...";
					$messageClass->form_data = $data;
					$u = \App\User::find(1);
					$u->notify(new \App\Notifications\ApiError($messageClass));
					throw new \Exception("Invalid or missing Site/Club Name ({$data[$site_field]})...");
				}
				break;
		}

		//////////////////////
		// Special Lead Name
		//
		$data["lead_name"] = ucwords(strtolower("{$data['first_name']} {$data['last_name']}"));

		////////////////////////////////////////
		// Do some checks on "Division" field
		//
		$div_field = isset($data['division']) ? "division" : "divison";
		$data[$div_field] = ucwords(strtolower(trim(preg_replace("/(private|public)(.*)/i", "$1 Division", $data[$div_field]))));

		return $data;
	}

	/**
	 * Takes a lead_type which will be either "event" or "member" and Using the various class variables
	 * this builds the final json array for the Reserve Interactive CRM push based on the fields as required
	 * for the lead_type and stores it in a class variable.
	 */
	protected function _buildJsonArrayForReserveInteractive($form_data)
	{
		$lead_type = strtolower($form_data['lead_type']);

		\Log::info("Lead Type: {$lead_type}");

		$header = [];
		$data   = [];
		$used   = [];
		$misc   = [];

		foreach(\Config::get("ri.fields.{$lead_type}") AS $subtype => $collection) {
			foreach($collection as $ri_field => $metadata) {
				foreach($metadata['possible'] as $ub_field) {
					if(isset($form_data[$ub_field])) {
						if(!in_array($ri_field, $header)) {
							$header[] = $ri_field;
							$data[]   = $form_data[$ub_field];
							$used[]   = $ub_field;
						}
					}
				}
			}
		}

		foreach($form_data as $k => $v) {
			if(!in_array($k, $used)) {
				$misc[] = ucwords(str_replace("_", " ", $k)) . " : {$v}";
			}
		}

		if(count($misc)) {
			$header[] = \Config::get("ri.fields.misc.{$lead_type}");
			$data[]   = implode("<br />\n", $misc);
		}

		$status_field = \Config::get("ri.fields.status.{$lead_type}");
		$fields = \Config::get("ri.fields.{$lead_type}.{$lead_type}-lead");
		$header[] = $status_field;
		$data[]   = $fields[$status_field]['values']['new'];

		$json['header'] = $header;
		$json['data'][] = $data;

		//Log::info(print_r($this->json,1));

		return $json;
	}
}
