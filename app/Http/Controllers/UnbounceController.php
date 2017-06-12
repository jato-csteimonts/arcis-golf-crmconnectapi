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
    protected $notes;

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
        $this->notes = [];
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
        // these are fields that we expect to be in the form_data object, we have to do the same
        // sanitize on all of them, so, we'll do it in a loop through this array later, some are arrays
        // some are not, just clean it up
        $expected_fields = [
            'name',
            'first_name',
            'last_name',
            'telephone',
            'email',
            'division',
            'club',
            'owner',
            'salesperson',
            'ip_address',
            'page_uuid',
            'variant',
            'time_submitted',
            'date_submitted',
            'page_url',
            'page_name',
            'spouse',
            'street_address',
            'city',
            'state',
            'zip'
        ];

        if (isset($form_data->notes[0])) {
            array_push($this->notes, 'notes : ' . $form_data->notes[0]);
        }

        foreach ($form_data as $form_field_key => $form_field_value) {
            $form_field_key = strtolower($form_field_key);
            if (in_array($form_field_key, $expected_fields)) {
                $this->unbounce->{$form_field_key} = $form_field_value[0];
            } else {
                array_push($this->notes, $form_field_key . " : " . $form_field_value[0]);
            }
        }

        $this->unbounce->notes = implode("\n", $this->notes);
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
        if ($lead_type == 'event') { // if it's an "event" lead_type
            $this->json = [
                'header' => [
                    'lead.site.name',
                    'lead.salesperson.emailAddress',
                    'lead.owner.emailAddress',
                    'lead.division.name',
                    'lead.name',
                    'lead.contact.firstName',
                    'lead.contact.lastName',
                    'lead.contact.email',
                    'lead.customData(0).tx00',
                    'lead.leadStatus',
                    'clubLead.contact.mobilePhone',
                    'clubLead.contact.mailingAddress.address1',
                    'clubLead.contact.mailingAddress.city',
                    'clubLead.contact.mailingAddress.state',
                    'clubLead.contact.mailingAddress.zipCode'

                ],
                'data' => [
                    [
                        $this->unbounce->club,
                        $this->unbounce->salesperson,
                        $this->unbounce->owner,
                        $this->unbounce->division,
                        $this->last_name . ' Event',
                        $this->first_name,
                        $this->last_name,
                        $this->unbounce->email,
                        $this->unbounce->notes,
                        'New',
                        $this->unbounce->telephone,
                        $this->unbounce->street_address,
                        $this->unbounce->city,
                        $this->unbounce->state,
                        $this->unbounce->zip

                    ]
                ]
            ];
        } elseif ($lead_type == 'member') { // if it's a "member" lead type
            $this->json = [
                'header' => [
                    'clubLead.club',
                    'clubLead.site.name',
                    'clubLead.salesperson.emailAddress',
                    'clubLead.owner.emailAddress',
                    'clubLead.division.name',
                    'clubLead.name',
                    'clubLead.contact.firstName',
                    'clubLead.contact.lastName',
                    'clubLead.contact.email',
                    'clubLead.customData(0).tx00',
                    'clubLead.leadStatus',
                    'clubLead.customData(0).tx03',
                    'clubLead.contact.mobilePhone',
                    'clubLead.contact.mailingAddress.address1',
                    'clubLead.contact.mailingAddress.city',
                    'clubLead.contact.mailingAddress.state',
                    'clubLead.contact.mailingAddress.zipCode'


                ],
                'data' => [
                    [
                        $this->unbounce->club,
                        $this->unbounce->club,
                        $this->unbounce->salesperson,
                        $this->unbounce->owner,
                        $this->unbounce->division,
                        $this->first_name . ' ' . $this->last_name,
                        $this->first_name,
                        $this->last_name,
                        $this->unbounce->email,
                        $this->unbounce->notes,
                        'New',
                        $this->unbounce->spouse,
                        $this->unbounce->telephone,
                        $this->unbounce->street_address,
                        $this->unbounce->city,
                        $this->unbounce->state,
                        $this->unbounce->zip

                    ]
                ]
            ];
        }

        return $this->json;

    }
}
