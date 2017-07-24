<?php

namespace App\Http\Controllers;

use App\Domain;
use App\Field;
use App\Webforms;
use Illuminate\Http\Request;

class WebformController extends LeadController
{

    public function serve_js()
    {
        return response()->view('javascripts.webform')->header('Content-Type', 'application/javascript');
    }

    public function process(Request $request)
    {
        // Uses the parent to save the raw request data to the leads table
        $lead = parent::_saveRawToLeadsTable($request, 'webform', 'webforms');

        // get the domain id
        $http_referer = parse_url($request->server('HTTP_REFERER'), PHP_URL_HOST);
        $domain = Domain::where('domain', '=', $http_referer)->first();

        // decodes the form data for later use
        $form_data = $request->all();

        $webform = new Webforms();
        $webform->lead_id = $lead->id;
        $webform->domain_id = $domain->id;
        $webform->form_data = serialize($request->all());
        $webform->save();



        // runs the webform through the field mapping
        // todo: we're assuming that ALL web forms are "member" forms for now... in the future, we might need to catch if it's an "event" form or a "member" form
        $notes = [];
        $header = [];
        $data = [];
        foreach ($request->all() as $incoming_field_key => $incoming_field_value) {
            try {
                $field = Field::where('from', '=', $incoming_field_key)->firstOrFail();
                if (!in_array($field->to, $header)) {
                    array_push($header, $field->to);
                    if (is_array($incoming_field_value)) {
                        if (isset($incoming_field_value['und'][0]['email'])) {
                            $v = $incoming_field_value['und'][0]['email'];
                        } elseif (isset($incoming_field_value['und'][0]['value'])) {
                            $v = $incoming_field_value['und'][0]['value'];
                        }
                        array_push($data, $v);
                    } else {
                        array_push($data, $incoming_field_value);
                    }
                }
            } catch (\Exception $e) {
                if (!is_array($incoming_field_value)) {
                    array_push($notes, $incoming_field_key . ': ' . $incoming_field_value);
                } else {
                    if (isset($incoming_field_value['und'][0]['value'])) {
                        if (count($incoming_field_value['und'][0]['value']) > 1) {
                            foreach ($incoming_field_value['und'][0]['value'] as $ukey => $uvalue) {
                                array_push($notes, $ukey . ': ' . $uvalue);
                            }
                        } else {
                            array_push($notes, $incoming_field_key . ': ' . $incoming_field_value['und'][0]['value']);
                        }
                    } else {
                        print_r($incoming_field_value);
                        if (isset($incoming_field_value['und'][0])) {
                            if (is_array($incoming_field_value['und'][0])) {
                                foreach ($incoming_field_value['und'][0] as $ukey => $uvalue) {
                                    array_push($notes, $ukey . ': ' . $uvalue);
                                }
                            }
                        } elseif(isset($incoming_field_value['und'])) {
                            if (is_array($incoming_field_value['und'])) {
                                foreach ($incoming_field_value['und'] as $ukey => $uvalue) {
                                    array_push($notes, $ukey . ': ' . $uvalue);
                                }
                            }
                        }
                    }
                }
            }
        }


        $first_name_position = array_search('clubLead.contact.firstName', $header);
        $last_name_position = array_search('clubLead.contact.lastName', $header);
        $full_name = null;
        if ($first_name_position) {
            $full_name = $data[$first_name_position];
        }
        if ($last_name_position) {
            if ($full_name) {
                $full_name = ' ' . $data[$last_name_position];
            } else {
                $full_name = $data[$last_name_position];
            }
        }

        array_push($header,
            'clubLead.club',
            'clubLead.site.name',
            'clubLead.salesperson.emailAddress',
            'clubLead.owner.emailAddress',
            'clubLead.division.name',
            'clubLead.leadStatus',
            'clubLead.name',
            'clubLead.customData(0).tx00');
        array_push($data,
            $domain->club,
            $domain->club,
            $domain->salesperson,
            $domain->owner,
            $domain->division,
            'New',
            $full_name,
            implode('<br />', $notes));



        $json = [
            'header' => $header,
            'data' => [$data]
        ];

//        print_r($header);
//        print_r($data);
//
//        return("here");

        // publishes to whatever is in the publish_to array using the parent's publish function
        parent::_publish([
            'ReserveInteractive' => [
                'json' => $json,
                'form_data' => $form_data,
                'lead' => $lead
            ]
        ]);

    }

}
