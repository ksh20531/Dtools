<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GolfReservation;

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
}
