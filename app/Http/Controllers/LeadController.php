<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Lead;
use App\Unbounce;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    protected $client;

    public function __construct()
    {
        // ReserveInteractive Connection Items
        // todo: this, and all ReserveInteractive, should be refactored out of this controller eventually and into a job/queue item perhaps
        $this->client = new Client([
            'base_uri' => 'https://www.reservecloud.com/gateway/request',
            'timeout' => 5.0,
        ]);

        // End ReserveInteractive Connection Items
    }



    public function postEventLead(Array $json = [], $mode = 'apply')
    {


    }

    public function unbounceWebhook(Request $request)
    {
        $lead = parent::_saveRawToLeadsTable($request, 'unbounce');

        $form_data = json_decode($request->data_json);

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

        $unbounce = new Unbounce();
        $unbounce->lead_id = $lead->id;

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
            $unbounce->{$expected_field} = $v;
        }

        $unbounce->save();

        // try to figure out a first and last name
        $first_name = $unbounce->name;
        $last_name = '';
        $flast = explode(' ', $unbounce->name, 2);
        if (isset($flast[1])) {
            $last_name = $flast[1];
            $first_name = $flast[0];
        }

        // push to the crm
        $this->postEventLead([
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
                    $unbounce->club,
                    $unbounce->salesperson,
                    $unbounce->owner,
                    $unbounce->division,
                    'the name of the event', // todo: we need to figure this out how it comes in from unbounce
                    $first_name,
                    $last_name,
                    $unbounce->email,
                    $unbounce->notes,
                    'New'
//                    'referral type', //todo: we need to know what this is from unbounce, or, hard-coded

                ]
            ]
        ], 'apply');

    }











    /**
     * Takes a method, a request name, and a max results int and returns the body of the request if successful
     * and false if fails.
     *
     * @param $method string
     * @param $requestName string
     * @param $maxResults int
     * @return bool|\Psr\Http\Message\StreamInterface
     */
    private function _makeReserveInteractiveRequest($method, $requestName, $maxResults)
    {
        try {
            $response = $this->client->request($method, '', [
                'auth' => [
                    env('RESERVE_INTERACTIVE_USERNAME'),
                    env('RESERVE_INTERACTIVE_PASSWORD')
                ],
                'query' => [
                    'requestName' => $requestName,
                    'requestGuid' => md5(date('YmdHis')),
                    'maxResults' => $maxResults
                ]
            ]);
            $body = $response->getBody();
            return $body;
        } catch (\Exception $e) {
            return false;
        }
    }
}
