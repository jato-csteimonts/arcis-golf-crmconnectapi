<?php

namespace App\Http\Controllers\Admin;

use App\Add;
use App\Club;
use App\Digitallead;
use App\Websitelead;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function update(Request $request)
    {
        $clubs = [];
        // first, get rid of all the existing data in the current database table "adds"
        Add::truncate();
        Digitallead::truncate();
        Websitelead::truncate();

        $paths = ['2016arm.xlsx', '2017arm.xlsx'];

        foreach ($paths as $path) {

            // roll through the "adds" spreadsheet and put the info into the
            $addsSS = Excel::load(Storage::path($path))->get();

            // roll through the arm spreadsheet and put the into the empty database table
            foreach ($addsSS as $add) {
                // match club to get the club id
                $club = Club::where('title', '=', $add->getTitle())->first();
                if (!$club) {
                    $club = new Club();
                    $club->title = $add->getTitle();
                    $club->save();
                }

                foreach ($add as $row) {
                    $add = new Add();
                    $add->club_id = $club->id;
                    $add->member_number = $row->membernumber;
                    $add->first_name = $row->firstname;
                    $add->last_name = $row->lastname;
                    $add->spouse_first_name = $row->spfirstname;
                    $add->spouse_last_name = $row->splastname;
                    $add->email = $row->email;
                    $add->spouse_email = $row->spemail;
                    $add->phone = $this->_normalizePhoneNumber($row->phone);
                    $add->address_1 = $row->address1;
                    $add->address_2 = $row->address2;
                    $add->city = $row->city;
                    $add->state = $row->state;
                    $add->zip = $row->zip;
                    $add->member_type = $row->membertype;
                    $add->monthly_dues = (!is_numeric($row->monthlydues) ? null : $row->monthlydues);
                    $add->joined_at = $row->joindate;
                    $add->initiation_fee = $row->initiationfee;
                    $add->save();
                }

            }
        }

        // go through the other two spreadsheets and try to match on the database
        $total_digital_leads = 0;

        $pcmcs = Excel::load(Storage::path('pcmc.xlsx'))->get();
        foreach ($pcmcs as $pcmc) {
            $club = Club::where('title', '=', $pcmc->getTitle())->first();
            foreach ($pcmc as $row) {
                if ($row->date_submitted != 'Date submitted') {
                    $dl = new Digitallead();
                    $dl->club_id = $club->id;
                    $dl->date_submitted = $row->date_submitted;
                    $dl->first_name = $row->first_name;
                    $dl->last_name = $row->last_name;
                    $dl->email = $row->email;
                    $dl->phone_number = $row->phone_number;
                    $dl->channel = $row->channel;
                    $dl->save();
                }
                //////////////// look for a match
                $add = Add::where('club_id', '=', $club->id)
                    ->where( function($query) use ($row) {
                        $query
                            ->where('email', '=', $row->email)
                            ->orWhere('spouse_email', '=', $row->email)
                            ->orWhere( function($query2) use ($query, $row) {
                                $query2->where('first_name', '=', $row->first_name)
                                        ->where('last_name', '=', $row->last_name)
                                        ->whereNotNull('first_name')
                                        ->whereNotNull('last_name');
                            })
                            ->orWhere( function($query3) use ($query, $row) {
                                $query3->where('spouse_first_name', '=', $row->first_name)
                                        ->where('spouse_last_name', '=', $row->last_name)
                                        ->whereNotNull('spouse_first_name')
                                        ->whereNotNull('spouse_last_name');
                            });
                        if ($this->_normalizePhoneNumber($row->phone_number) != 0 && !is_null($this->_normalizePhoneNumber($row->phone_number)) && !is_null($row->phone_number)) {
                            $query->orWhere('phone', '=', $this->_normalizePhoneNumber($row->phone_number));
                        }
                    })
                    ->first();
                if ($add) {
                    $add->digitallead_id = $dl->id;
                    $add->marketed_at = $row->date_submitted;
                    $add->membership_interest = $row->membership_interest;
                    $add->channel = $row->channel;
                    $add->matched_on = json_encode([
                        'email' => $row->email,
                        'phone_number' => $row->phone_number,
                        'first_name' => $row->first_name,
                        'last_name' => $row->last_name
                    ]);
                    $add->save();
                }
            }

        }


        // go through the other two spreadsheets and try to match on the database
        $pcmwls = Excel::load(Storage::path('pcmwl.xlsx'))->get();
        foreach ($pcmwls as $pcmwl) {
            $club = Club::where('title', '=', $pcmwl->getTitle())->first();
            foreach ($pcmwl as $row) {
                if ($row->date_submitted != 'Date submitted') {
                    $wl = new Websitelead();
                    $wl->club_id = $club->id;
                    $wl->form_type = $row->form_type;
                    $wl->date_submitted = $row->date_submitted;
                    $wl->first_name = $row->first_name;
                    $wl->last_name = $row->last_name;
                    $wl->email = $row->email;
                    $wl->phone_number = $row->phone_number;
                    $wl->how_did_you_hear = $row->how_did_you_hear_about_us;
                    $wl->membership_type = $row->membership_type;
                    $wl->comments = $row->comments;
                    $wl->save();
                }


                //////////////// look for a match
                $add = Add::where('club_id', '=', $club->id)
                    ->where( function($query) use ($row) {
                        $query
                            ->where('email', '=', $row->email)
                            ->orWhere('spouse_email', '=', $row->email)
                            ->orWhere( function($query2) use ($query, $row) {
                                $query2->where('first_name', '=', $row->first_name)
                                    ->where('last_name', '=', $row->last_name)
                                    ->whereNotNull('first_name')
                                    ->whereNotNull('last_name');
                            })
                            ->orWhere( function($query3) use ($query, $row) {
                                $query3->where('spouse_first_name', '=', $row->first_name)
                                    ->where('spouse_last_name', '=', $row->last_name)
                                    ->whereNotNull('spouse_first_name')
                                    ->whereNotNull('spouse_last_name');
                            });
                        if ($this->_normalizePhoneNumber($row->phone_number) != 0 && !is_null($this->_normalizePhoneNumber($row->phone_number)) && !is_null($row->phone_number)) {
                            $query->orWhere('phone', '=', $this->_normalizePhoneNumber($row->phone_number));
                        }
                    })
                    ->first();
                if ($add) {
                    if ($row->date_submitted != 'Date submitted') {
                        $add->websitelead_id = $wl->id;
                        $add->marketed_at = $row->date_submitted;
                        $add->form_type = $row->form_type;
                        $add->channel = 'website lead';
                        $add->matched_on = json_encode([
                            'email' => $row->email,
                            'phone_number' => $row->phone_number,
                            'first_name' => $row->first_name,
                            'last_name' => $row->last_name
                        ]);
                        $add->save();
//                        echo("<pre>");
//                        print_r($add->toArray());
//                        print_r($row);
//                        echo("\n\n~~~~~~~~~~~~~~~~~~~~~\n\n");
                    }
                }
            }
        }





//        return redirect('/admin/reports/show');
    }

    public function show()
    {
        $dl = Digitallead::all();
        $wl = Websitelead::all();
        $clubs = Club::all();

        foreach ($clubs as $club) {
            $club->total_monthly_dues = Add::select('monthly_dues')
                ->where('club_id', '=', $club->id)
                ->sum('monthly_dues');

            $club->member_types = Add::select('member_type', DB::raw('count(*) as total'))
                ->where('club_id', '=', $club->id)
                ->whereNotNull('channel')
                ->groupBy('member_type')
                ->get();

            $club->digitalLeadsConverted = Add::where('club_id', '=', $club->id)
                ->whereNotNull('channel')
                ->where('channel', '!=', 'website lead')
                ->count();

            $club->websiteLeadsConverted = Add::where('club_id', '=', $club->id)
                ->where('channel', '=', 'website lead')
                ->count();

        }

        return view('web.report', [
            'dl' => $dl,
            'wl' => $wl,
            'clubs' => $clubs
        ]);

    }

    private function _normalizePhoneNumber($phone_number)
    {
        return preg_replace('~\D~', '', $phone_number);
    }
}
