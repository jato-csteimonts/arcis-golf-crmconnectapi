<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Field;
use App\Webforms;
use Illuminate\Http\Request;

class WebformController extends LeadController
{

    public function serve_js()
    {
        return response()->view('javascripts.webform')->header('Content-Type', 'application/javascript');
    }

    public function process(Request $request)
    {
    	//\Log::info(print_r($request->toArray(),1));

        // Uses the parent to save the raw request data to the leads table
        $lead = parent::_saveRawToLeadsTable($request, 'webform', 'webforms');

        // get the domain id
	    $http_referer = str_replace("www.", "", parse_url($request->server('HTTP_REFERER'), PHP_URL_HOST));
        $domain = Domain::where('domain', '=', $http_referer)->first();

	    //\Log::info(print_r($domain->toArray(),1));

        // decodes the form data for later use
        $form_data = $request->all();

        $webform = new Webforms();
        $webform->lead_id = $lead->id;
        $webform->domain_id = $domain->id;
        $webform->form_data = serialize($request->all());
        $webform->save();

        // runs the webform through the field mapping
        // todo: we're assuming that ALL web forms are "member" forms for now... in the future, we might need to catch if it's an "event" form or a "member" form
	    $form_data  = [];
	    $form_data['club']        = $domain->site_code;
	    $form_data['division']    = $domain->division;
	    $form_data['owner']       = $domain->owner;
	    $form_data['salesperson'] = $domain->salesperson;
	    $form_data['lead_type']   = "member";

        foreach ($request->all() as $incoming_field_key => $incoming_field_value) {
        	$field = preg_replace(["/^field_/", "/_$/"], "", $incoming_field_key);
        	switch(true) {
		        case !is_array($incoming_field_value):
		        	$value = $incoming_field_value;
		        	break;
		        case is_array($incoming_field_value['und']):
			        $tmp_data  = $incoming_field_value['und'][0];
			        $keys = array_keys($tmp_data);
			        $value = $tmp_data[$keys[0]];
		        	break;
		        default:
			        $value = $incoming_field_value['und'];
		        	break;
	        }
	        $form_data[$field] = $value;
        }

	    //\Log::info(print_r($form_data,1));

	    $data = $this->_normalizeInputAndAddToUnbounceObject($form_data);

        //\Log::info(print_r($data,1));

	    parent::_publish([
		    'ReserveInteractive' => [
			    'json' => $this->_buildJsonArrayForReserveInteractive($data),
			    'form_data' => $data,
			    'lead' => $lead
		    ]
	    ]);

    }

}
