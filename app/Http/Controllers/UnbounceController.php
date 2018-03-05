<?php

namespace App\Http\Controllers;

use App\Unbounce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnbounceController extends LeadController
{
    protected $unbounce;
    protected $first_name;
    protected $last_name;
    protected $json;
    protected $publish_to;
    protected $misc;

    /**
     * Sets the ubounce data object.
     * Sets the json empty array.
     *
     * UnbounceController constructor.
     */
    public function __construct()
    {
        $this->publish_to = [];
        $this->unbounce = new Unbounce();
        $this->misc = [];
        $this->json = [];
    }

    /**
     * Takes a request and launches other methods to:
     * 1) Save the raw lead info to the leads table
     * 2) Save the unbounce info to the unbounces table
     * 3) Dispatches a job to push the data to the Reserve Interactive CRM
     *
     * @param Request $request
     */
    public function webhook(Request $request)
    {
        // Uses the parent to save the raw request data to the leads table
        $lead = parent::_saveRawToLeadsTable($request, 'unbounce', 'unbounces');

        // sets the lead id for the unbounces table
        $this->unbounce->lead_id = $lead->id;

        // decodes the form data for later use
        $form_data = json_decode($request->data_json);

        // adds the form data to the unbounce object
        $this->_normalizeInputAndAddToUnbounceObject($form_data);

        // saves the unbounce data to the table
        $this->unbounce->save();

        // tries to figure out first and last name
        $this->_extractFirstAndLastName();

        // publishes to whatever is in the publish_to array using the parent's publish function
        parent::_publish([
            'ReserveInteractive' => [
                'json' => $this->_buildJsonArrayForReserveInteractive($form_data->lead_type[0]),
                'form_data' => $form_data,
                'lead' => $lead
                ]
        ]);


    }

    /**
     * Takes form data and cleans it up, then, adds it to the appropriate places for the unbounce
     * data model object.
     *
     * @param $form_data
     */
    private function _normalizeInputAndAddToUnbounceObject($form_data)
    {
	    $sites = \Config::get("ri.sites");

    	$data = [];
	    foreach ($form_data as $k => $curr_data) {
		    $data[strtolower($k)] = $curr_data[0];
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
					throw new Exception("Invalid or missing Site/Club Name ({$data[$site_field]})...");
				}
		    	break;
	    }

	    ////////////////////////////////////////
	    // Do some checks on "Division" field
	    //
	    $div_field = isset($data['division']) ? "division" : "divison";
	    $data[$div_field] = ucwords(strtolower(trim(preg_replace("/(private|public)(.*)/i", "$1 Division", $data[$div_field]))));

	    $this->unbounce->form_data = serialize($data);
    }

    /**
     * Using the unbounce "name" field from the unbounce object, this tries to figure out a first and
     * last name and then saves the first and last name to the class variables.
     */
    private function _extractFirstAndLastName()
    {
        if (isset($this->unbounce->name)) {
            $this->first_name = $this->unbounce->name;
            $this->last_name = '';
            $flast = explode(' ', $this->unbounce->name, 2);
            if (isset($flast[1])) {
                $this->last_name = $flast[1];
                $this->first_name = $flast[0];
            }
        }

        if (isset($this->unbounce->first_name)) {
            $this->first_name = $this->unbounce->first_name;
        }

        if (isset($this->unbounce->last_name)) {
            $this->last_name = $this->unbounce->last_name;
        }
    }

    /**
     * Takes a lead_type which will be either "event" or "member" and Using the various class variables
     * this builds the final json array for the Reserve Interactive CRM push based on the fields as required
     * for the lead_type and stores it in a class variable.
     */
    private function _buildJsonArrayForReserveInteractive($lead_type)
    {
	    $lead_type = strtolower($lead_type);

		$header = [];
	    $data   = [];
	    $used   = [];
	    $misc   = [];

	    $form_data = unserialize($this->unbounce->form_data);

	    foreach(\Config::get("ri.fields.{$lead_type}") AS $subtype => $collection) {
	    	foreach($collection as $ri_field => $metadata) {
				foreach($metadata['unbounce'] as $ub_field) {
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

	    $this->json['header'] = $header;
	    $this->json['data'][] = $data;

	    //Log::info(print_r($this->json,1));

	    return $this->json;
    }
}
