<?php

//set_curier_location.php?currier_id=1&lat=34.5435&long=4556.34656

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';

$currier_id = isset($_GET['currier_id']) ? $_GET['currier_id'] : null;
$latitudine = isset($_GET['lat']) ? $_GET['lat'] : null;
$longitudine = isset($_GET['long']) ? $_GET['long'] : null;

setCurierLocation($currier_id, $latitudine, $longitudine);

function setCurierLocation($curier_id, $latitudine, $longitudine)
{

    $apiCall = 'curl -v -X PUT -H "Content-Type: application/json" --data "@moveToHBASE"';
    $apiCallPart = array();
    $apiCallPart[] = '{"key":"' . base64_encode(time() . "_" . $curier_id) . '", "Cell": [{"column":"' . base64_encode('c:lat') . '", "$":"' . base64_encode($latitudine) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode(time() . "_" . $curier_id) . '", "Cell": [{"column":"' . base64_encode('c:long') . '", "$":"' . base64_encode($longitudine) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode(time() . "_" . $curier_id) . '", "Cell": [{"column":"' . base64_encode('c:curier_id') . '", "$":"' . base64_encode($curier_id) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode(time() . "_" . $curier_id) . '", "Cell": [{"column":"' . base64_encode('c:time') . '", "$":"' . base64_encode(time()) . '"}]} ';

    file_put_contents(__DIR__ . '/moveToHBASE', '{"Row":[' . implode(', ', $apiCallPart) . ']}');
    $apiCall .= " " . HBASE_API_URL . "/courier_route/" . time();

    return exec($apiCall);
}
