<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Field;

class FieldController extends Controller
{
    protected $ri_fields;

    public function __construct()
    {
        $this->ri_fields = [
            'Member' => [
                'Lead' => [
                    'clubLead.club',
                    'clubLead.site.name',
                    'clubLead.division.name',
                    'clubLead.name',
                    'clubLead.customData(0).tx00',
                    'clubLead.leadStatus',
                    'clubLead.customData(0).tx03',
                ],
                'Owner' => [
                    'clubLead.owner.emailAddress',
                ],
                'Primary Contact' => [
                    'clubLead.contact.firstName',
                    'clubLead.contact.lastName',
                    'clubLead.contact.email',
                    'clubLead.contact.mobilePhone',
                    'clubLead.contact.mailingAddress.address1',
                    'clubLead.contact.mailingAddress.city',
                    'clubLead.contact.mailingAddress.state',
                    'clubLead.contact.mailingAddress.zipCode'
                ],
                'Salesperson' => [
                    'clubLead.salesperson.emailAddress',
                ]
            ],
            'Event' => [
                'Lead' => [
                    'lead.site.name',
                    'lead.division.name',
                    'lead.name',
                    'lead.customData(0).tx00',
                    'lead.leadStatus',
                ],
                'Owner' => [
                    'lead.owner.emailAddress',
                ],
                'Primary Contact' => [
                    'lead.contact.firstName',
                    'lead.contact.lastName',
                    'lead.contact.email',
                    'clubLead.contact.mobilePhone',
                    'clubLead.contact.mailingAddress.address1',
                    'clubLead.contact.mailingAddress.city',
                    'clubLead.contact.mailingAddress.state',
                    'clubLead.contact.mailingAddress.zipCode'
                ],
                'Salesperson' => [
                    'lead.salesperson.emailAddress',
                ]
            ]
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fields = Field::all();

        return view('web.fields.index', [
            'fields' => $fields
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $field = new Field();

        return view('web.fields.form', [
            'field' => $field,
            'ri_fields' => $this->ri_fields
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $field = Field::firstOrNew([
            'to' => $request->get('to'),
            'from' => $request->get('from')
        ], [
            'to' => $request->get('to'),
            'from' => $request->get('from')
        ]);
        $field->save();

        return redirect('/admin/fields');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $field = Field::find($id);

        return view('web.fields.form', [
            'field' => $field,
            'ri_fields' => $this->ri_fields
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $field = Field::find($id);
        $field->to = $request->get('to');
        $field->from = $request->get('from');
        $field->save();

        return redirect('/admin/fields');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $field = Field::find($id);
        $field->delete();
        return redirect('/admin/fields');
    }
}
