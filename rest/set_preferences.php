<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';

$currier_id = isset($_GET['currier_id']) ? $_GET['currier_id'] : null;
$suppliers_distance = isset($_GET['suppliers_distance']) ? $_GET['suppliers_distance'] : null;
$suppliers_duration = isset($_GET['suppliers_duration']) ? $_GET['suppliers_duration'] : null;
$delivery_distance = isset($_GET['delivery_distance']) ? $_GET['delivery_distance'] : null;
$delivery_duration = isset($_GET['delivery_duration']) ? $_GET['delivery_duration'] : null;

$apiCall = 'curl -v -X PUT -H "Content-Type: application/json" --data "@moveToHBASE"';

$apiCallPart = array();

if ($suppliers_distance) {
    $apiCallPart[] = '{"key":"' . base64_encode($currier_id) . '", "Cell": [{"column":"' . base64_encode('p:suppliers_distance') . '", "$":"' . base64_encode($suppliers_distance) . '"}]} ';
}

if ($suppliers_duration) {
    $apiCallPart[] = '{"key":"' . base64_encode($currier_id) . '", "Cell": [{"column":"' . base64_encode('p:suppliers_duration') . '", "$":"' . base64_encode($suppliers_duration) . '"}]} ';
}

if ($delivery_distance) {
    $apiCallPart[] = '{"key":"' . base64_encode($currier_id) . '", "Cell": [{"column":"' . base64_encode('p:delivery_distance') . '", "$":"' . base64_encode($delivery_distance) . '"}]} ';
}

if ($delivery_duration) {
    $apiCallPart[] = '{"key":"' . base64_encode($currier_id) . '", "Cell": [{"column":"' . base64_encode('p:delivery_duration') . '", "$":"' . base64_encode($delivery_duration) . '"}]} ';
}

file_put_contents(__DIR__ . '/moveToHBASE', '{"Row":[' . implode(', ', $apiCallPart) . ']}');
$apiCall .= " " . HBASE_API_URL . "/preferences/" . time();
exec($apiCall);