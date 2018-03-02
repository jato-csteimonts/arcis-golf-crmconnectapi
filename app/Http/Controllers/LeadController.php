<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Lead;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;

use App\Jobs\postReserveInteractiveLead;

class LeadController extends Controller
{
    /**
     * Takes a request object and a source string and saves the raw lead information. Returns the lead object.
     *
     * @param $request object
     * @param $source string
     * @return Lead object
     */
    protected function _saveRawToLeadsTable($request, $source, $table)
    {
        $lead = new Lead();
        $lead->source = $source;
        $lead->table = $table;
        $lead->raw = serialize($request->all());
        $lead->save();
        return $lead;
    }

    protected function _publish($publish_to)
    {
        // todo: add try/catch to this loop
        foreach ($publish_to as $key => $value) {
            $func = '_publishTo' . $key;
            $this->$func($value);
        }
    }

    protected function _publishToReserveInteractive($v)
    {
        $requestName = 'MemberLeadImport';
        if (isset($v['form_data']->lead_type[0])) {
            if ($v['form_data']->lead_type[0]  == 'event')
            $requestName = 'EventLeadImport';
        }
	    try {
		    $this->dispatch(new postReserveInteractiveLead($requestName, $v['json'], $v['lead']->id));
	    } catch (\Exception $e) {
		    $u = \App\User::find(1);
		    $u->notify(new \App\Notifications\ApiError(json_decode($e->getMessage())));
		    abort(500, $e->getMessage());
	    }
    }
}
