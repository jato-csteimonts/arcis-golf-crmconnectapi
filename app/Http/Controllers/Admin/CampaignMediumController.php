<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserClub;

class CampaignMediumController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$campaign_mediums = \App\CampaignMedium::orderBy("code", "ASC")->get();

		return view('web.campaigns.mediums.index', [
			'campaign_mediums' => $campaign_mediums
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$campaign_medium = new \App\CampaignMedium();

		return view('web.campaigns.mediums.edit', [
			'campaign_medium' => $campaign_medium
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
		$campaign_medium = new \App\CampaignMedium();
		$campaign_medium->code = str_pad(preg_replace("/([^0-9]*)/", "", $request->code), 2, "0", STR_PAD_LEFT);
		$campaign_medium->name = $request->name;
		$campaign_medium->slug = $request->slug;
		$campaign_medium->save();

		return $this->update($request, $campaign_medium->id);
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
			$campaign_medium = \App\CampaignMedium::findOrFail($id);
		} catch (\Exception $e) {
			return redirect("/admin/campaign-mediums");
		}

		return view('web.campaigns.mediums.edit', [
			'campaign_medium' => $campaign_medium
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
			$campaign_medium = \App\CampaignMedium::find($id);

			$campaign_medium->update([
				"name" => $request->name,
				"code" => str_pad(preg_replace("/([^0-9]*)/", "", $request->code), 2, "0", STR_PAD_LEFT),
				"slug" => $request->slug,
			]);

			//\Log::info(print_r($request->toArray(),1));

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: CampaignMediumController::update()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
			return redirect("/admin/campaign-mediums");
		}

		return redirect("/admin/campaign-mediums/{$id}/edit");
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

			$campaign_medium = \App\CampaignMedium::find($id);

			///////////////////////////
			// Delete Campaign Medium
			//
			$campaign_medium->delete();

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: CampaignMediumController::destroy()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
		}

		return redirect("/admin/campaign-mediums/");

	}
}
