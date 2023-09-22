<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dashboard;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dashboard.index');
    }

    public function list()
    {
        $dashboards = Dashboard::rightJoin('users','users.id','dashboards.user_id')
                                ->where('dashboards.deleted',0)
                                ->select(
                                    "*",
                                    "dashboards.id as dashboard_id",
                                    "dashboards.created_at as dashboard_created_at",
                                )
                                ->orderBy('dashboard_created_at','desc')
                                ->paginate(10);

        return view('dashboard.list',[
            'dashboards' => $dashboards,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if($id != 0){
            $dashboard = Dashboard::find($id);

            return view('dashboard.dashboard',[
                'type' => 'modify',
                'id' => $id,
                'dashboard' => $dashboard
            ]);
        }else{
            return view('dashboard.dashboard',[
                'type' => 'create',
                'id' => $id
            ]);
        }

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $title = $request->get('title');
        $content = $request->get('content');

        try{
            if($id != 0){
                $dashboard = Dashboard::find($id);
                $result = 'edit_success';
            }else{
                $dashboard = new Dashboard;
                $result = 'create_success';
            }
            $dashboard->title = $title;
            $dashboard->content = $content;
            $dashboard->user_id = \Auth::user()->id;
            $dashboard->save();

            $return_id = $dashboard->id;

            return [
                'result' => $result,
                'return_id' => $return_id
            ];

        }catch(Exception $e){
            return 'fail';
        }
        

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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $dashboard = Dashboard::find($id);
            $dashboard->deleted = 1;
            $dashboard->deleted_at = now();
            $dashboard->save();

            return 'success';
        }catch(Exception $e){
            return 'fail';
        }
        
    }
}
