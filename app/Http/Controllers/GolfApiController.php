<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GolfReservation;
use App\Models\GolfResult;
use App\Models\GolfField;

class GolfApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reservations = GolfReservation::with('field')
                                    ->where('deleted',0)
                                    ->orderBy('id','desc')
                                    ->limit(14)
                                    ->get();

        return response()->json([
                "success" => "success",
                "response" => $reservations,
                "code" => 200
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
        \Log::info("GolfApiController::store".$request->collect());

        $new_reservation = new GolfReservation;
        $new_reservation->field_id = $request->get('field_id');
        $new_reservation->reservation_time = $request->get('reserve_time');
        $new_reservation->save();

        $reservation = GolfReservation::with('field')->find($new_reservation->id);

        return response()->json([
                "success" => "success",
                "response" => $reservation,
                "code" => 200
            ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $reservation = GolfReservation::with('field')->find($id);

        return response()->json([
                "success" => "success",
                "response" => $reservation,
                "code" => 200
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
        $reservation = GolfReservation::with('field')->find($id);

        $reservation->field_id = $request->get('field_id');
        $reservation->reservation_time = $request->get('reserve_time');
        $reservation->save();

        return response()->json([
                "success" => "success",
                "response" => $reservation,
                "code" => 200
            ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reservation = GolfReservation::with('field')->find($id);
        $reservation->deleted = 1;
        $reservation->save();

        return response()->json([
                "success" => "success",
                "response" => $reservation,
                "code" => 200
            ]);
    }

    public function getResult(Request $request){
        $start = date('Y-m-d', strtotime($request->get('start')));
        
        if($request->get('end') == null || $request->get('end') == "")
            $end = $start;
        else
            $end = date('Y-m-d', strtotime($request->get('end')));

        $field_names = ['일죽','여주','설악'];
        $fields = GolfField::whereIn('name',$field_names)->get('id');

        $results = GolfResult::with('reservation.field')
                        ->whereHas('reservation.field', function($q) use($fields){
                            $q->whereIn('id',$fields);
                        })
                        ->whereHas('reservation', function($q){
                            $q->where('deleted',0);
                        })
                        ->whereRaw("DATE_FORMAT(booked_time,'%Y-%m-%d') BETWEEN '$start' AND '$end'")
                        ->get();

        $data = array(
            'sulak' => $sulak = array(
                'count' => 0,
                'success' => 0
            ),
            'iljuk' => $iljuk = array(
                'count' => 0,
                'success' => 0
            ),
            'yeoju' => $yeoju = array(
                'count' => 0,
                'success' => 0
            ),
            'etc' => $etc = array(
                'count' => 0,
                'success' => 0
            ),
            'total' => $total = array(
                'count' => 0,
                'success' => 0
            )
        );

        foreach($results as $result){
            if($result->reservation->field->name == '설악'){
                $data['sulak']['count'] = $data['sulak']['count'] + 1;
                if($result->is_booked == 1){
                    $data['sulak']['success'] = $data['sulak']['success'] + 1;
                    $data['total']['success'] = $data['total']['success'] + 1;
                }
            }
            else if($result->reservation->field->name == '일죽'){
                $data['iljuk']['count'] = $data['iljuk']['count'] + 1;
                if($result->is_booked == 1){
                    $data['iljuk']['success'] = $data['iljuk']['success'] + 1;
                    $data['total']['success'] = $data['total']['success'] + 1;
                }
            }
            else if($result->reservation->field->name == '여주'){
                $data['yeoju']['count'] = $data['yeoju']['count'] + 1;
                if($result->is_booked == 1){
                    $data['yeoju']['success'] = $data['yeoju']['success'] + 1;
                    $data['total']['success'] = $data['total']['success'] + 1;
                }
            }
            else{
                $data['etc']['count'] = $data['etc']['count'] + 1;
                if($result->is_booked == 1){
                    $data['etc']['success'] = $data['etc']['success'] + 1;
                    $data['total']['success'] = $data['total']['success'] + 1;
                }
            }
            $data['total']['count'] = $data['total']['count'] + 1;
        }

        return response()->json($data);
    }
}
