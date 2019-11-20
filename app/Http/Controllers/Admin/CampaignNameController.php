<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserClub;

class CampaignNameController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$campaign_names = \App\CampaignName::orderBy("name", "ASC")->get();

		return view('web.campaigns.names.index', [
			'campaign_names' => $campaign_names
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$campaign_name = new \App\CampaignName();

		return view('web.campaigns.names.edit', [
			'campaign_name' => $campaign_name
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
		$campaign_name = new \App\CampaignName();
		$campaign_name->name        = $request->name;
		$campaign_name->slug        = $request->slug;
		$campaign_name->description = $request->description ?? "";
		$campaign_name->club_id     = $request->club_id;
		$campaign_name->save();

		return $this->update($request, $campaign_name->id);
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
			$campaign_name = \App\CampaignName::findOrFail($id);
		} catch (\Exception $e) {
			return redirect("/admin/campaign-names");
		}

		return view('web.campaigns.names.edit', [
			'campaign_name' => $campaign_name
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
			$campaign_name = \App\CampaignName::find($id);

			$campaign_name->update([
				"name"        => $request->name,
				"slug"        => $request->slug,
				"description" => $request->description ?? "",
				"club_id"     => $request->club_id,
			]);

			//\Log::info(print_r($request->toArray(),1));

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: CampaignNameController::update()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
			return redirect("/admin/campaign-names");
		}

		return redirect("/admin/campaign-names/{$id}/edit");
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

			$campaign_name = \App\CampaignName::find($id);

			///////////////////////////
			// Delete Campaign Medium
			//
			$campaign_name->delete();

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: CampaignNameController::destroy()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
		}

		return redirect("/admin/campaign-names/");

	}
}
