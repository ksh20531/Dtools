<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        \Log::info("BusController::index");

        return view('bus.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
        //
    }

    public function searchBus(Request $request)
    {
        \Log::info("BusController::searchBus");

        $busId = $request->get('busId');
        $busList = $this->getBusPosByRtidList($this->getBusRouteId($busId));
        \Log::info($busId);
        // \Log::info($busList);

        return $busList;
    }

    public function selectBus(Request $request)
    {
        \Log::info("BusController::selectBus");
        
        $busId = $request->get('busId');
        // $selectedBusRouteId = $request->get('selectedBusRouteId');
        \Log::info($busId);
        $routeList = $this->getStaionsByRouteList($this->getBusRouteId($busId));

        return [
            'routeList' => $routeList,
            // 'selectedBusRouteId' => $selectedBusRouteId,
        ];
    }

    public function selectStation(Request $request)
    {
        \Log::info("BusController::selectStation : ".$request->collect());

        $busId = $request->get('busId');
        $stationId = $request->get('stationId');
        $ord = $request->get('ord');
        $busRouteId = $request->get('busRouteId');
        $selectedBusRouteId = $request->get('selectedBusRouteId');


        // selectedBus의 현제 seq 값을 받아와야 함.
        $busList = $this->getBusPosByRtidList($this->getBusRouteId($busId));
        foreach ($busList as $key => $bus) {
            if($bus[1] == $selectedBusRouteId){
                $selectedBusOrd = $bus[2];
            }else{
                $selectedBusOrd = 0;
            }
        }



        $busInfo = $this->getArrInfoByRouteList($stationId,$ord,$busRouteId,$selectedBusRouteId);
        \Log::info($busInfo);

        // 
        if($busInfo['firstBus']['busNumber'] == $selectedBusRouteId){
            $stationNm = $busInfo['firstBus']['stationNm'];
            $arrMsg = $busInfo['firstBus']['arrMsg'];
            $leftStations = 0;
        }else if($busInfo['secondBus']['busNumber'] == $selectedBusRouteId){
            $stationNm = $busInfo['secondBus']['stationNm'];
            $arrMsg = $busInfo['secondBus']['arrMsg'];
            $leftStations = 0;
        }else{
            $stationNm = $busInfo['firstBus']['stationNm'];
            $arrMsg = '';
            $leftStations = $selectedBusOrd - $ord;
        }

        return [
            'stationNm' => $stationNm,
            'arrMsg' => $arrMsg,
            // 'leftStations' => $leftStations,
        ];
    }

    public function getBusRouteId($busId)
    {
        \Log::Info("BusController::getBusRouteId ". $busId);

        $ch = curl_init();
        $url = 'http://ws.bus.go.kr/api/rest/busRouteInfo/getBusRouteList'; /*URL*/
        $queryParams = '?' . urlencode('serviceKey') . "=". env('OPEN_API_BUS_ENCODING_KEY'); /*Service Key*/
        $queryParams .= '&' . urlencode('strSrch') . '=' . $busId; /**/

        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $busRouteId = $xml->msgBody->itemList->busRouteId;

        return $busRouteId;
    }

    public function getBusPosByRtidList($busRouteId)
    {
        \Log::Info("BusController::getBusPosByRtidList");

        $ch = curl_init();
        $url = 'http://ws.bus.go.kr/api/rest/buspos/getBusPosByRtid'; /*URL*/
        $queryParams = '?' . urlencode('serviceKey') . "=". env('OPEN_API_BUS_ENCODING_KEY'); /*Service Key*/
        $queryParams .= '&' . urlencode('busRouteId') . '=' . $busRouteId; /**/

        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $itemList = $xml->msgBody->itemList;

        $busList = [];
        foreach ($itemList as $key => $bus) {
            $items = [];
            $plainNo = substr($bus->plainNo,-4);
            array_push($items, (int)$busRouteId);
            array_push($items, (string)$plainNo);
            array_push($items, (int)$bus->sectOrd);
            array_push($busList, $items);
        }

        return $busList;
    }

    public function getStaionsByRouteList($busRouteId)
    {
        \Log::info("BusController::getStaionsByRouteList");

        $ch = curl_init();
        $url = 'http://ws.bus.go.kr/api/rest/busRouteInfo/getStaionByRoute'; /*URL*/
        $queryParams = '?' . urlencode('serviceKey') . "=". env('OPEN_API_BUS_ENCODING_KEY'); /*Service Key*/
        $queryParams .= '&' . urlencode('busRouteId') . '=' . $busRouteId; /**/

        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $itemList = $xml->msgBody->itemList;

        $routeList = [];
        foreach ($itemList as $key => $route) {
            $items = [];
            array_push($items, (int)$route->station);
            array_push($items, (string)$route->stationNm);
            array_push($items, (int)$route->seq);
            array_push($items, (int)$busRouteId);
            array_push($routeList, $items);
        }

        return $routeList;
    }

    public function getArrInfoByRouteList($stationId,$ord,$busRouteId,$selectedBusRouteId)
    {
        \Log::info("BusController::getArrInfoByRouteList");

        \Log::info($stationId);
        \Log::info($ord);
        \Log::info($busRouteId);
        
        $ch = curl_init();
        $url = 'http://ws.bus.go.kr/api/rest/arrive/getArrInfoByRoute'; /*URL*/
        $queryParams = '?' . urlencode('serviceKey') . "=". env('OPEN_API_BUS_ENCODING_KEY'); /*Service Key*/
        $queryParams .= '&' . urlencode('stId') . '=' . $stationId; /**/
        $queryParams .= '&' . urlencode('busRouteId') . '=' . $busRouteId; /**/
        $queryParams .= '&' . urlencode('ord') . '=' . $ord; /**/

        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $stationNm = $xml->msgBody->itemList->stNm;
        $firstBusNumber = substr($xml->msgBody->itemList->plainNo1,-4);
        $firstBusArrMsg = $xml->msgBody->itemList->arrmsg1;
        $firstBusStationOrd = $xml->msgBody->itemList->staOrd;

        $secondBusNumber = substr($xml->msgBody->itemList->plainNo2,-4);
        $secondBusArrMsg = $xml->msgBody->itemList->arrmsg2;
        $secondBusStationOrd = $xml->msgBody->itemList->staOrd2;

        $firstBus = [
            'stationNm' => (string)$stationNm,
            'busNumber' => (int)$firstBusNumber,
            'arrMsg' => (string)$firstBusArrMsg,
            'ord' => (int)$firstBusStationOrd,
        ];
        $secondBus = [
            'stationNm' => (string)$stationNm,
            'busNumber' => (int)$secondBusNumber,
            'arrMsg' => (string)$secondBusArrMsg,
            'ord' => (int)$secondBusStationOrd,
        ];

        return [
            'firstBus' => $firstBus,
            'secondBus' => $secondBus
        ];

    }
}
