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

        $unbounce = new Unbounce();
        $unbounce->name = (isset($form_data->name) ? $form_data->name : null);
        $unbounce->telephone = (isset($form_data->telephone) ? $form_data->telephone : null);
        $unbounce->save();

    }

    public function anotherWebhook(Request $request)
    {
        $this->_saveRawToLeadsTable($request, 'anotehr');
    }

    private function _saveRawToLeadsTable($request, $source)
    {
        $lead = new Lead();
        $lead->source = $source;
        $lead->raw = serialize($request->all());
        $lead->save();
    }
}
