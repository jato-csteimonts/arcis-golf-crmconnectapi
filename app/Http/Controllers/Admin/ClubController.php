<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Domain;
use App\User;
use App\UserClub;
use App\UserRole;

class ClubController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$clubs = \App\Club::orderBy("name", "ASC")->get();

		return view('web.clubs.index', [
			'clubs' => $clubs
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$club = new \App\Club();

		return view('web.clubs.edit', [
			'club' => $club
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
		$club = new \App\Club();
		$club->name = $request->name;
		$club->division = $request->division;
		$club->active = $request->active;
		$club->site_code = $request->site_code;
		$club->save();

		return $this->update($request, $club->id);
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
		try {
			$club = \App\Club::findOrFail($id);
		} catch (\Exception $e) {
			return redirect("/admin/clubs");
		}

		return view('web.clubs.edit', [
			'club' => $club
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
		try {
			$club = \App\Club::find($id);

			$club->update([
				"name" => $request->name,
				"division" => $request->division,
				"active" => $request->active,
				"site_code" => $request->site_code,
			]);

			//\Log::info(print_r($request->toArray(),1));

			////////////////////////////////
			// First edit existing Domains
			//
			foreach($request->domains['existing'] ?? [] as $domain_id => $domain_name) {
				if(trim($domain_name)) {
					try {
						Domain::findOrFail($domain_id)->update([
							"domain" => $domain_name
						]);
					} catch (\Exception $e) {}
				}
			}

			///////////////////////////////////////////////////
			// Next delete any Domains that have been deleted
			//
			if(count($request->domains['existing'] ?? [])) {
				Domain::where("club_id", $club->id)
				      ->whereNotIn("id", array_keys($request->domains['existing']))
				      ->delete();
			}

			/////////////////////////
			// Next add new Domains
			//
			foreach($request->domains['new'] ?? [] as $index => $domain_name) {
				if(trim($domain_name)) {
					$Domain = new Domain();
					$Domain->club_id = $club->id;
					$Domain->domain = $domain_name;
					$Domain->save();
				}
			}

			////////////////////////////////////////////////////////////////////////
			// Delete any User assignments to this club that may have been removed
			//
			UserClub::where("club_id", $club->id)
			        ->whereNotIn("user_id", $request->{"users-selected"} ?? [])
			        ->delete();

			///////////////////////////////////////////
			// Add any newly added users to the club
			//
			foreach($request->{"users-selected"} ?? [] AS $user_id) {
				try {
					UserClub::where("club_id", $club->id)
					        ->where("user_id", $user_id)
					        ->firstOrFail();
				} catch (\Exception $e) {
					$UserClub = new UserClub();
					$UserClub->club_id = $club->id;
					$UserClub->user_id = $user_id;
					$UserClub->save();
				}
			}

			//////////////////////////
			// Go through User Roles
			//
			foreach($request->roles ?? [] as $sub_role => $user_id) {

				UserRole::where("club_id", $club->id)
				        ->where("sub_role", $sub_role)
				        ->delete();

				if($user_id) {

					$UserRole = new UserRole();
					$UserRole->club_id = $club->id;
					$UserRole->user_id = $user_id;
					$UserRole->role = "owner";
					$UserRole->sub_role = $sub_role;
					$UserRole->save();

					$UserRole = new UserRole();
					$UserRole->club_id = $club->id;
					$UserRole->user_id = $user_id;
					$UserRole->role = "salesperson";
					$UserRole->sub_role = $sub_role;
					$UserRole->save();

				}

			}

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: ClubController::update()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
			return redirect("/admin/clubs");
		}

		return redirect("/admin/clubs/{$id}/edit");
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		try {

			$club = \App\Club::find($id);

			////////////////////////////////////
			// Delete User / Club assignments
			//
			UserClub::where("club_id", $club->id)
			        ->delete();

			///////////////////
			// Delete Domains
			//
			Domain::where("club_id", $club->id)
			        ->delete();

			//////////////////////
			// Delete User Roles
			//
			UserRole::where("club_id", $club->id)
			        ->delete();

			////////////////
			// Delete Club
			//
			$club->delete();

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: ClubController::destroy()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
		}

		return redirect("/admin/clubs/");

	}
}
