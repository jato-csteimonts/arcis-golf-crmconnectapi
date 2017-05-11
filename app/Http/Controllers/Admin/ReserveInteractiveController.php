<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\ReserveInteractive;

class ReserveInteractiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reserveinteractives = ReserveInteractive::all();

        return view('web.reserveinteractives.index', [
            'reserveinteractives' => $reserveinteractives
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $reserveinteractive = new ReserveInteractive();

        return view('web.reserveinteractives.form', [
            'reserveinteractive' => $reserveinteractive
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
        $reserveinteractive = new ReserveInteractive();
        $reserveinteractive->name = $request->get('name');
        $reserveinteractive->email = $request->get('email');
        $reserveinteractive->password = bcrypt(md5(date('YmdHis') . rand(0,10000) . rand(0, 10000)));
        $reserveinteractive->save();

        return redirect('/admin/reserveinteractives');
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
        $reserveinteractive = ReserveInteractive::find($id);

        return view('web.reserveinteractives.form', [
            'reserveinteractive' => $reserveinteractive
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
        $reserveinteractive = ReserveInteractive::find($id);
        $reserveinteractive->name = $request->get('name');
        $reserveinteractive->email = $request->get('email');
        $reserveinteractive->save();

        return redirect('/admin/reserveinteractives');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reserveinteractive = ReserveInteractive::find($id);
        $reserveinteractive->delete();
        return redirect('/adminr/reserveinteractives');
    }
}
