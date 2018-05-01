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
	    Log::info(print_r($_SERVER,1));
	    Log::info(print_r($request->toArray(),1));

	    Log::info(json_encode($request->toArray()));
	    Log::info(serialize($_SERVER));

        // Uses the parent to save the raw request data to the leads table
        $lead = parent::_saveRawToLeadsTable($request, 'unbounce', 'unbounces');

        // sets the lead id for the unbounces table
        $this->unbounce->lead_id = $lead->id;

        // decodes the form data for later use
        $form_data = json_decode($request->data_json);

        // adds the form data to the unbounce object
        // $this->_normalizeInputAndAddToUnbounceObject($form_data);

	    $data = $this->_normalizeInputAndAddToUnbounceObject($form_data);

	    $this->unbounce->form_data = serialize($data);

        // saves the unbounce data to the table
        $this->unbounce->save();

        // tries to figure out first and last name
        $this->_extractFirstAndLastName();

        // publishes to whatever is in the publish_to array using the parent's publish function
        parent::_publish([
            'ReserveInteractive' => [
                'json' => $this->_buildJsonArrayForReserveInteractive($data),
                'form_data' => $data,
                'lead' => $lead
                ]
        ]);


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

}
