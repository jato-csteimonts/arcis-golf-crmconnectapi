<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

use App\Lead;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public function ajax(Request $request) {

		//\Log::info("Controller::ajax()");
		//\Log::info(print_r($request->toArray(),1));

		$response = [];

		switch($request->action) {
			case "edit-club-add-domain":
				$response["HTML"] = view('web.clubs.subviews.domain', ['domain' => new \App\Domain()])->render();
				break;
			case "edit-user-get-roles":
				$response["HTML"] = view('web.users.subviews.roles', ['club' => \App\Club::find($request->club_id)])->render();
				break;
		}

		//\Log::info(print_r($response,1));
		return json_encode($response);

	}


}
