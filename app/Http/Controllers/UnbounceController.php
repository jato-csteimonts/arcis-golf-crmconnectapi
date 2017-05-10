<?php

namespace App\Http\Controllers;

use App\Jobs\postReserveInteractiveLead;
use App\Unbounce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UnbounceController extends Controller
{
    protected $unbounce;
    protected $first_name;
    protected $last_name;
    protected $json;

    public function __construct()
    {
        $this->unbounce = new Unbounce();
        $this->json = [];
    }

    public function webhook(Request $request)
    {
        $lead = parent::_saveRawToLeadsTable($request, 'unbounce', 'unbounces');

        $this->unbounce->lead_id = $lead->id;
        $form_data = json_decode($request->data_json);
        $this->_normalizeInputAndAddToUnbounceObject($form_data);

        $this->unbounce->save();

        $this->_extractFirstAndLastName();

        $this->_buildJsonArray();

        $this->dispatch(new postReserveInteractiveLead('EventLeadImport', $this->json));

    }

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
            'page_name'
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
     * @return array
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

    private function _buildJsonArray()
    {
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
                    'the name of the event', // todo: we need to figure this out how it comes in from unbounce
                    $this->first_name,
                    $this->last_name,
                    $this->unbounce->email,
                    $this->unbounce->notes,
                    'New'
//                    'referral type', //todo: we need to know what this is from unbounce, or, hard-coded

                ]
            ]
        ];
    }
}
