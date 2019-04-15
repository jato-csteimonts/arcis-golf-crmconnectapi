<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserClub;
use App\UserRole;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();

        return view('web.users.index', [
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
	    $user = new User();

	    return view('web.users.edit', [
		    'user' => $user
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
	    $user = new \App\User();
	    $user->name = $request->name;
	    $user->email = $request->email;
	    $user->is_admin = $request->is_admin ?? 0;

	    if($request->password) {
		    $user->password = \Hash::make($request->password);
	    }

	    $user->save();

	    return $this->update($request, $user->id);
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
		    $user = \App\User::findOrFail($id);
	    } catch (\Exception $e) {
		    return redirect("/admin/users");
	    }

	    return view('web.users.edit', [
		    'user' => $user
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
		    $user = \App\User::findOrFail($id);

		    $input = [
			    "name" => $request->name,
			    "email" => $request->email,
			    "is_admin" => $request->is_admin ?? 0,
		    ];

		    if($request->password) {
			    $input["password"] = \Hash::make($request->password);
		    }

		    $user->update($input);

		    \Log::info(print_r($request->toArray(),1));

		    ////////////////////////////////////////////////////////////////////////
		    // Delete any Club assignments to this user that may have been removed
		    //
		    UserClub::where("user_id", $user->id)
		            ->whereNotIn("club_id", $request->{"clubs-selected"} ?? [])
		            ->delete();

		    ///////////////////////////////////////////
		    // Add any newly added users to the club
		    //
		    foreach($request->{"clubs-selected"} ?? [] AS $club_id) {
			    try {
				    UserClub::where("club_id", $club_id)
				            ->where("user_id", $user->id)
				            ->firstOrFail();
			    } catch (\Exception $e) {
				    $UserClub = new UserClub();
				    $UserClub->club_id = $club_id;
				    $UserClub->user_id = $user->id;
				    $UserClub->save();
			    }
		    }

		    //////////////////////////
		    // Go through User Roles
		    //
		    foreach($request->roles ?? [] as $club_id => $roles) {
			    $request->session()->flash('roles_club_id', $club_id);
			    foreach($roles as $sub_role => $user_id) {

				    UserRole::where("club_id", $club_id)
				            ->where("sub_role", $sub_role)
				            ->delete();

				    if($user_id) {

					    $UserRole = new UserRole();
					    $UserRole->club_id = $club_id;
					    $UserRole->user_id = $user_id;
					    $UserRole->role = "owner";
					    $UserRole->sub_role = $sub_role;
					    $UserRole->save();

					    $UserRole = new UserRole();
					    $UserRole->club_id = $club_id;
					    $UserRole->user_id = $user_id;
					    $UserRole->role = "salesperson";
					    $UserRole->sub_role = $sub_role;
					    $UserRole->save();

				    }

			    }
		    }



	    } catch (\Exception $e) {
		    \Log::info("****************************************");
		    \Log::info("ERROR LOCATION: UserController::update()");
		    \Log::info("ERROR MESSAGE: " . $e->getMessage());
		    \Log::info("ERROR FILE: " . $e->getFile());
		    \Log::info("ERROR LINE: " . $e->getLine());
		    \Log::info("****************************************");
		    return redirect("/admin/users");
	    }

	    return redirect("/admin/users/{$id}/edit");

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

		    $user = \App\User::findOrtFail($id);

		    ////////////////////////////////////
		    // Delete User / Club assignments
		    //
		    UserClub::where("user_id", $user->id)
		            ->delete();

		    //////////////////////
		    // Delete User Roles
		    //
		    UserRole::where("user_id", $user->id)
		            ->delete();

		    ////////////////
		    // Delete Club
		    //
		    $user->delete();

	    } catch (\Exception $e) {
		    \Log::info("****************************************");
		    \Log::info("ERROR LOCATION: UserController::destroy()");
		    \Log::info("ERROR MESSAGE: " . $e->getMessage());
		    \Log::info("ERROR FILE: " . $e->getFile());
		    \Log::info("ERROR LINE: " . $e->getLine());
		    \Log::info("****************************************");
	    }

	    return redirect("/admin/users/");
    }
}
