<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Alarm;
use App\Models\Routine;
use Auth;

class AlarmController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('alarm.index');
    }

    public function getRoutine(){
        \Log::info("AlarmController::getAlarm");

        $routines = Alarm::rightJoin('routines',function($q){
                                $q->on('alarms.routine_id','=','routines.id')
                                    ->where('routines.deleted',0);
                            })
                            ->select(
                                '*',
                                'alarms.id as alarm_id',
                                'alarms.title as alarm_title',
                            )
                            ->where('routines.deleted',0)
                            ->where('user_id',1)
                            ->orderBy('routines.id','desc')
                            ->orderBy('alarms.day','asc')
                            ->orderBy('alarms.hour','asc')
                            ->orderBy('alarms.minute','asc')
                            ->get()
                            ->groupBy('id');
        
        $today = date('w');

        $alarms = Alarm::where('deleted',0)
                        ->where('day',$today+1)
                        ->select(
                            'id',
                            'title',
                            'day',
                            'hour',
                            'minute',
                        )
                        ->orderBy('alarms.day','asc')
                        ->orderBy('alarms.hour','asc')
                        ->orderBy('alarms.minute','asc')
                        ->get();

        return view('alarm.routine',[
            'routines' => $routines,
            'alarms' => $alarms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        \Log::info("AlarmController::create");

        $type = $request->get('type');

        if($type == 'routine'){
            try{
                $routine = new Routine;
                $routine->user_id = 1;
                $routine->title = $request->get('title');
                $routine->save();

                return 'success';
            }catch(Exception $e){
                return 'fail';
            }
        }else if($type == 'alarm'){
            try{
                $alarm = new Alarm;
                $alarm->routine_id = $request->get('routine_id');
                $alarm->title = $request->get('title');
                $alarm->day = $request->get('day');
                $alarm->hour = $request->get('hour');
                $alarm->minute = $request->get('minute');
                $alarm->save();

                return 'success';
            }catch(Exception $e){
                return 'fail';
            }
        }
        
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
    public function show(Request $request,$id)
    {
        \Log::info("AlarmController::show ".$id);

        try{
            $alarm = Alarm::find($id);

            return $alarm;
        }catch(Exception $e){
            return 'fail';
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        \Log::info("AlarmController::update ".$id);

        $type = $request->get('type');

        if($type == 'routine'){
            try{
                $routine = Routine::find($id);
                $routine->title = $request->get('title');
                $routine->save();

                return 'success';
            }catch(Exception $e){

            }
        }else if($type == 'alarm'){
            try{
                $alarm = Alarm::find($id);
                $alarm->routine_id = $request->get('routine_id');
                $alarm->title = $request->get('title');
                $alarm->day = $request->get('day');
                $alarm->hour = $request->get('hour');
                $alarm->minute = $request->get('minute');
                $alarm->save();

                return 'success';
            }catch(Exception $e){
                return 'fail';
            }
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        \Log::info("AlarmController::destroy ".$id);
        try{
            $routine = Routine::find($id);
            $routine->deleted = 1;
            $routine->save();

            return "success";
        }catch(Exception $e){
            return 'fail';
        }
    }
}
