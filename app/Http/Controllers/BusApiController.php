<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bus;
use Auth;

class BusApiController extends Controller
{
    public function searchBus(Request $request)
    {
        $busStr = $request->get('busStr');
        $busList = $this->getBusRouteId($busStr);
        $user_id = $request->get('user_id');

        $bus = Bus::where('user_id',$user_id)
                ->get();
        
        foreach($busList->itemList as $item){
            $item->is_marked = 0;
            foreach($bus as $b){
                if($item->busRouteNm == $b->bus_route_name)
                    $item->is_marked = $b->is_marked;
            }
        }

        if(count($busList->itemList) == 1)
            $busList->itemList->busCount = 1;

        return response()->json($busList);
    }

    public function getBus(Request $request)
    {
        $busRouteId = $request->get('busRouteId');
        $busList = $this->getBusPosByRtidList($busRouteId);
        $routeList = $this->getStaionsByRouteList($busRouteId);

        foreach($busList->itemList as $bus){
            foreach($routeList->itemList as $route){
                if(json_encode($bus->sectOrd) == json_encode($route->seq))
                    $bus->stationNm = (string)$route->stationNm;
            }
        }

        if(count($busList->itemList) == 1)
            $busList->itemList->busCount = 1;

        return response()->json($busList);
    }

    public function selectBus(Request $request)
    {    
        $busRouteId = $request->get('busRouteId');
        $routeList = $this->getStaionsByRouteList($busRouteId);

        return response()->json($routeList);
    }

    public function selectStation(Request $request)
    {
        \Log::info("BusApiController::selectStation : ".$request->collect());
        $busStr = $request->get('busStr');
        $stationId = $request->get('stationId');
        $ord = $request->get('ord');
        $busRouteId = $request->get('busRouteId');
        $selectedBusNumber = $request->get('selectedBusNumber');

        $busList = $this->getBusPosByRtidList($busRouteId);

        $selectedBusOrd = 0;
        foreach($busList->itemList as $bus)
        {
            if(strpos($bus->plainNo,$selectedBusNumber)){
                $selectedBusOrd = $bus->sectOrd;
            }
        }

        $busInfo = $this->getArrInfoByRouteList($stationId,$ord,$busRouteId,$selectedBusNumber);

        $stationNm = $busInfo->msgBody->itemList->stNm;
        $firstBusNumber = substr($busInfo->msgBody->itemList->plainNo1,-4);
        $firstBusArrMsg = $busInfo->msgBody->itemList->arrmsg1;
        $firstBusStationOrd = $busInfo->msgBody->itemList->staOrd;
        $firstBus = [
            'stationNm' => (string)$stationNm,
            'busNumber' => (int)$firstBusNumber,
            'arrMsg' => (string)$firstBusArrMsg,
            'ord' => (int)$firstBusStationOrd,
        ];

        // $secondBusNumber = substr($busInfo->msgBody->itemList->plainNo2,-4);
        // $secondBusArrMsg = $busInfo->msgBody->itemList->arrmsg2;
        // $secondBusStationOrd = $busInfo->msgBody->itemList->staOrd2;
        // $secondBus = [
        //     'stationNm' => (string)$stationNm,
        //     'busNumber' => (int)$secondBusNumber,
        //     'arrMsg' => (string)$secondBusArrMsg,
        //     'ord' => (int)$secondBusStationOrd,
        // ];
        
        if($firstBus['busNumber'] == $selectedBusNumber){
            $stationNm = $firstBus['stationNm'];
            $arrMsg = $firstBus['arrMsg'];
            $leftStations = 0;
        }else{
            $stationNm = $firstBus['stationNm'];
            $arrMsg = '';
            $leftStations = $ord - $selectedBusOrd;
        }

        return [
            'stationNm' => $stationNm,
            'arrMsg' => $arrMsg,
            'leftStations' => $leftStations,
        ];
    }

    public function getBookMark(Request $request)
    {
        $user_id = $request->get('user_id');

        $bus = Bus::where('user_id',$user_id)
                    ->where('is_marked',1)
                    ->get();

        return $bus;
    }

    public function bookMark(Request $request)
    {
        $busRouteId = $request->get('busRouteId');
        $busStr = $request->get('busRouteNm');
        $user_id = $request->get('user_id');
        $is_marked = $request->get('is_marked');

        try{
            $bus = Bus::where('bus_route_id',$busRouteId)
                    ->where('bus_route_name',$busStr)
                    ->where('user_id',$user_id)
                    ->first();

            if(empty($bus)){
                $bus = new Bus;
                $bus->bus_route_id = $busRouteId;
                $bus->bus_route_name = $busStr;
                $bus->user_id = $user_id;
                $bus->is_marked = $is_marked;
                $bus->save();
            }else{
                $bus->bus_route_id = $busRouteId;
                $bus->bus_route_name = $busStr;
                $bus->user_id = $user_id;
                $bus->is_marked = $is_marked;
                $bus->save();
            }

            return 'success';
        }catch(Exception $e){
            return 'fail';
        }
    }

    public function deleteBookMark(Request $request)
    {
        $busRouteId = $request->get('busRouteId');
        $busStr = $request->get('busRouteNm');
        $user_id = $request->get('user_id');

        $bus = Bus::where('bus_route_id',$busRouteId)
                    ->where('bus_route_name',$busStr)
                    ->where('user_id',$user_id)
                    ->first();

        $bus->is_marked = 0;
        $bus->save();

        return 'success';
    }

    public function getBusRouteId($busStr)
    {
        $ch = curl_init();
        $url = 'http://ws.bus.go.kr/api/rest/busRouteInfo/getBusRouteList'; /*URL*/
        $queryParams = '?' . urlencode('serviceKey') . "=". env('OPEN_API_BUS_ENCODING_KEY'); /*Service Key*/
        $queryParams .= '&' . urlencode('strSrch') . '=' . $busStr; /**/

        curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

        $xml = simplexml_load_string($response);
        $busList = $xml->msgBody;

        return $busList;
    }

    public function getBusPosByRtidList($busRouteId)
    {
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
        $itemList = $xml->msgBody;

        return $itemList;
    }

    public function getStaionsByRouteList($busRouteId)
    {
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
        $itemList = $xml->msgBody;

        return $itemList;
    }

    public function getArrInfoByRouteList($stationId,$ord,$busRouteId,$selectedBusNumber)
    {
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

        return $xml;

    }
}
