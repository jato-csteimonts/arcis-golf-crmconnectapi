<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\UserClub;

class LeadController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$leads = \App\Leads\Base::whereNull("duplicate_of")
		                        ->orderBy("created_at", "DESC")
		                        ->get();

		return view('web.leads.index', [
			'leads' => $leads
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create()
	{
		$campaign_term = new \App\CampaignTerm();

		return view('web.campaigns.terms.edit', [
			'campaign_term' => $campaign_term
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
		$campaign_term = new \App\CampaignTerm();
		$campaign_term->name        = $request->name;
		$campaign_term->slug        = $request->slug;
		$campaign_term->description = $request->description ?? "";
		$campaign_term->code        = str_pad(preg_replace("/([^0-9]*)/", "", $request->code), 4, "0", STR_PAD_LEFT);
		//$campaign_term->club_id     = $request->club_id;
		$campaign_term->save();

		return $this->update($request, $campaign_term->id);
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
			$campaign_term = \App\CampaignTerm::findOrFail($id);
		} catch (\Exception $e) {
			return redirect("/admin/campaign-terms");
		}

		return view('web.campaigns.terms.edit', [
			'campaign_term' => $campaign_term
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
			$campaign_term = \App\CampaignTerm::find($id);

			$campaign_term->update([
				"name"        => $request->name,
				"slug"        => $request->slug,
				"description" => $request->description ?? "",
				"code"        => str_pad(preg_replace("/([^0-9]*)/", "", $request->code), 4, "0", STR_PAD_LEFT),
				//"club_id"     => $request->club_id,
			]);

			//\Log::info(print_r($request->toArray(),1));

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: CampaignTermController::update()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
			return redirect("/admin/campaign-terms");
		}

		return redirect("/admin/campaign-terms/{$id}/edit");
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

			$campaign_term = \App\CampaignTerm::find($id);

			///////////////////////////
			// Delete Campaign Medium
			//
			$campaign_term->delete();

		} catch (\Exception $e) {
			\Log::info("****************************************");
			\Log::info("ERROR LOCATION: CampaignTermController::destroy()");
			\Log::info("ERROR MESSAGE: " . $e->getMessage());
			\Log::info("ERROR FILE: " . $e->getFile());
			\Log::info("ERROR LINE: " . $e->getLine());
			\Log::info("****************************************");
		}

		return redirect("/admin/campaign-terms/");

	}
}