<?php

namespace App\Http\Controllers;

use App\Jobs\postReserveInteractiveLead;
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

    /**
     * Sets the ubounce data object.
     * Sets the json empty array.
     *
     * UnbounceController constructor.
     */
    public function __construct()
    {
        $this->publish_to = ['reserveinteractive'];
        $this->unbounce = new Unbounce();
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

        ///////////////////////////////////////////////////////////////////////////////////////
        // the class variable array of publish_to tells us what to do with the final data set
        ///////////////////////////////////////////////////////////////////////////////////////
        // todo: refactor this out to the LeadController perhaps?
        // Reserve Interactive CRM /////////////////////////
        if (in_array('reserveinteractive', $this->publish_to)) {

            // we expect a "lead_type" field to determine which requestName to use for Reserve Interactive
            if ($form_data->lead_type[0] == 'member') {
                $requestName = 'MemberLeadImport';
            } elseif ($form_data->lead_type[0] == 'event') {
                $requestName = 'EventLeadImport';
            }

            // builds the final json array
            $this->_buildJsonArrayForReserveInteractive($form_data->lead_type[0]);

            // dispatches the job that pushes to the Reserve Interactive CRM
            $this->dispatch(new postReserveInteractiveLead($requestName, $this->json, $lead->id));
        }

        // ... other publish_to conditionals can be added here (e.g. email, etc.)

        ///////////////////////////////////////////////////////////////////////////////////////
        /// end publish_to blocks
        ///////////////////////////////////////////////////////////////////////////////////////


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
            'telephone',
            'email',
            'division',
            'club',
            'owner',
            'salesperson',
            'notes',
            'ip_address',
            'page_uuid',
            'variant',
            'time_submitted',
            'date_submitted',
            'page_url',
            'page_name',
            'spouse'
        ];

        // loop through the expected fields in the form_data object and sanitize them, then,
        // add them to the unbounce object
        foreach ($expected_fields as $expected_field) {
            $v = null;
            if (isset($form_data->{$expected_field})) {
                if (is_array($form_data->{$expected_field})) {
                    $v = $form_data->{$expected_field}[0];
                } else {
                    $v = $form_data->{$expected_field};
                }
            }
            $this->unbounce->{$expected_field} = $v;
        }
    }

    /**
     * Using the unbounce "name" field from the unbounce object, this tries to figure out a first and
     * last name and then saves the first and last name to the class variables.
     */
    private function _extractFirstAndLastName()
    {
        $this->first_name = $this->unbounce->name;
        $this->last_name = '';
        $flast = explode(' ', $this->unbounce->name, 2);
        if (isset($flast[1])) {
            $this->last_name = $flast[1];
            $this->first_name = $flast[0];
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
                    'lead.leadStatus'
//                'lead.referral'

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
                        'New'
//                    'referral type', //todo: we need to know what this is from unbounce, or, hard-coded

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
                    'clubLead.customData(0).tx03'
//                'lead.referral'

                ],
                'data' => [
                    [
                        $this->unbounce->club,
                        $this->unbounce->club,
                        $this->unbounce->salesperson,
                        $this->unbounce->owner,
                        $this->unbounce->division,
                        $this->last_name . ' Member',
                        $this->first_name,
                        $this->last_name,
                        $this->unbounce->email,
                        $this->unbounce->notes,
                        'New',
                        $this->unbounce->spouse
//                    'referral type', //todo: we need to know what this is from unbounce, or, hard-coded

                    ]
                ]
            ];
        }

    }
}
