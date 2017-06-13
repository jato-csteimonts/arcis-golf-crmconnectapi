<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Webforms;
use Illuminate\Http\Request;

class WebformController extends LeadController
{
    public function process(Request $request)
    {
        // Uses the parent to save the raw request data to the leads table
        $lead = parent::_saveRawToLeadsTable($request, 'unbounce', 'unbounces');

        // get the domain id
        $http_referer = parse_url($request->server('HTTP_REFERER'), PHP_URL_HOST);
        $domain = Domain::where('domain', '=', $http_referer)->first();

        // decodes the form data for later use
        $form_data = json_decode($request->data_json);


        $webform = new Webforms();
        $webform->lead_id = $lead->id;
        $webform->domain_id = $domain->id;
        $webform->form_data = serialize($request->all());
        $webform->save();


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
}
