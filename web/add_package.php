<?php

require_once __DIR__ . '/../rest/config.php';
require_once __DIR__ . '/../rest/google_maps_api.php';
require_once __DIR__ . '/../rest/hbase.php';

//getLocationDetails($_POST);

//addPackage($_POST);
addAdress($_POST);


function addPackage($packageDetails){
    $apiCall = 'curl -v -X PUT -H "Content-Type: application/json" --data "@moveToHBASE"';

    $pkt_id = "2222";//todo

    $apiCallPart = array();
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:vol_height') . '", "$":"' . base64_encode($packageDetails['height']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:weight') . '", "$":"' . base64_encode($packageDetails['weight']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:vol_length') . '", "$":"' . base64_encode($packageDetails['length']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:vol_width') . '", "$":"' . base64_encode($packageDetails['width']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:status') . '", "$":"' . base64_encode('AVAILABLE') . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:courier_id') . '", "$":"' . base64_encode('null') . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:customer_id') . '", "$":"' . base64_encode('null') . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($pkt_id) . '", "Cell": [{"column":"' . base64_encode('p:supplier_id') . '", "$":"' . base64_encode('null') . '"}]} ';

    file_put_contents(__DIR__ . '/moveToHBASE', '{"Row":[' . implode(', ', $apiCallPart) . ']}');
    $apiCall .= " " . HBASE_API_URL . "/package/" . time();

    return exec($apiCall);
}

function addAdress($data){
    $apiCall = 'curl -v -X PUT -H "Content-Type: application/json" --data "@moveToHBASE"';

    $address_id = "22";//todo

    $apiCallPart = array();
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:city') . '", "$":"' . base64_encode($data['city']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:street') . '", "$":"' . base64_encode($data['street']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:number') . '", "$":"' . base64_encode($data['number']) . '"}]} ';

   // $locationDetails = getLocationDetails($data);
    $locationDetails = array("lat" => "45.73713", "lng" => "21.2184243");
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:lat') . '", "$":"' . base64_encode($locationDetails['lat']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:lng') . '", "$":"' . base64_encode($locationDetails['lng']) . '"}]} ';

    file_put_contents(__DIR__ . '/moveToHBASE', '{"Row":[' . implode(', ', $apiCallPart) . ']}');
    $apiCall .= " " . HBASE_API_URL . "/address/" . time();

    return exec($apiCall);

}


function getLocationDetails($data){
    $city = $data['city'];
    $street = $data['street'];
    $no = $data['number'];

    //https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=YOUR_API_KEY

    $res = curlRequest(getGoogleAPIUrl(
        GOOGLE_API_URL,
        'maps/api/geocode',
        GOOGLE_API_FORMAT,
        GOOGLE_API_KEY,
        [
            'address' => str_replace(" ", "+", $city).",+".str_replace(" ", "+", $street).",+".$no,
        ]
    ));

    return $res["results"][0]["geometry"]["location"];

}
