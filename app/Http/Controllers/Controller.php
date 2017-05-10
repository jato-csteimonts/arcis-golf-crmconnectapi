<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Lead;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

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
