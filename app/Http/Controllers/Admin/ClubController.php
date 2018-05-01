<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Domain;

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
		$domain = new Domain();

		return view('web.domains.form', [
			'clubs' => $domain
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
		$domain = new Domain();
		$domain->domain = $request->get('domain');
		$domain->owner = $request->get('owner');
		$domain->salesperson = $request->get('salesperson');
		$domain->club = $request->get('club');
		$domain->division = $request->get('division');
		$domain->save();

		return redirect('/admin/domains');
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
		$club = \App\Club::find($id);

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
		$domain = Domain::find($id);
		$domain->domain = $request->get('domain');
		$domain->owner = $request->get('owner');
		$domain->salesperson = $request->get('salesperson');
		$domain->club = $request->get('club');
		$domain->division = $request->get('division');
		$domain->save();

		return redirect('/admin/domains');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$domain = Domain::find($id);
		$domain->delete();
		return redirect('/admin/domains');
	}
}
