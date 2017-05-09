<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Lead;
use App\Unbounce;

class LeadController extends Controller
{

    public function unbounceWebhook(Request $request)
    {
        $this->_saveRawToLeadsTable($request, 'unbounce');

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

    }


    private function _saveRawToLeadsTable($request, $source)
    {
        $lead = new Lead();
        $lead->source = $source;
        $lead->raw = serialize($request->all());
        $lead->save();
    }
}
