<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Lead;

use GuzzleHttp\Client;

use Illuminate\Support\Facades\Log;

class LeadController extends Controller
{
    /**
     * Takes a request object and a source string and saves the raw lead information. Returns the lead object.
     *
     * @param $request object
     * @param $source string
     * @return Lead object
     */
    public function _saveRawToLeadsTable($request, $source, $table)
    {
        $lead = new Lead();
        $lead->source = $source;
        $lead->table = $table;
        $lead->raw = serialize($request->all());
        $lead->save();
        return $lead;
    }
}
