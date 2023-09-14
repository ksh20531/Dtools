<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BusApiController extends Controller
{
    public function searchBus(Request $request)
    {
        \Log::info("BusApiController::searchBus : ".$request->get('busId'));

        $busId = $request->get('busId');
        $busList = $this->getBusPosByRtidList($this->getBusRouteId($busId));
        $routeList = $this->getStaionsByRouteList($this->getBusRouteId($busId));

        foreach($busList->itemList as $bus){
            foreach($routeList->itemList as $route){
                if(json_encode($bus->sectOrd) == json_encode($route->seq))
                    $bus->stationNm = (string)$route->stationNm;
            }
        }

        return response()->json($busList);
    }

    public function selectBus(Request $request)
    {
        \Log::info("BusApiController::selectBus : ".$request->get('busRouteId'));
        
        $busRouteId = $request->get('busRouteId');
        $routeList = $this->getStaionsByRouteList($this->getBusRouteId($busRouteId));

        return response()->json($routeList);
    }

    public function selectStation(Request $request)
    {
        \Log::info("BusApiController::selectStation : ".$request->collect());

        $busId = $request->get('busId');
        $stationId = $request->get('stationId');
        $ord = $request->get('ord');
        $busRouteId = $request->get('busRouteId');
        $selectedBusNumber = $request->get('selectedBusNumber');

        $busList = $this->getBusPosByRtidList($this->getBusRouteId($busId));

        $selectedBusOrd = 0;
        foreach($busList->itemList as $bus)
        {
            if(strpos($bus->plainNo,$selectedBusNumber))
                $selectedBusOrd = $bus->sectOrd;
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

    public function getBusRouteId($busId)
    {
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
