<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Unbounce;

class UnbounceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $unbounces = Unbounce::all();

        return view('web.unbounces.index', [
            'unbounces' => $unbounces
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $unbounce = new Unbounce();

        return view('web.unbounces.form', [
            'unbounce' => $unbounce
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
        $unbounce = new Unbounce();
        $unbounce->name = $request->get('name');
        $unbounce->email = $request->get('email');
        $unbounce->password = bcrypt(md5(date('YmdHis') . rand(0,10000) . rand(0, 10000)));
        $unbounce->save();

        return redirect('/admin/unbounces');
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
        $unbounce = Unbounce::find($id);

        return view('web.unbounces.form', [
            'unbounce' => $unbounce
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
        $unbounce = Unbounce::find($id);
        $unbounce->name = $request->get('name');
        $unbounce->email = $request->get('email');
        $unbounce->save();

        return redirect('/admin/unbounces');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $unbounce = Unbounce::find($id);
        $unbounce->delete();
        return redirect('/adminr/unbounces');
    }
}
