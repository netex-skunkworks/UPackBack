<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';

$packageId = $_GET['package_id'];
$status = $_GET['status'];
$courierId = null;

if ($status == 'accepted') {
    $courierId = $_POST['courier_id'];
}

$apiCall = 'curl -v -X PUT -H "Content-Type: application/json" --data "@moveToHBASE"';

$rowKey = base64_encode($packageId);
$columnName = 'p:status';
$columnValue = $status;
$apiCallPart = array();
$apiCallPart[] = '{"key":"' . base64_encode($packageId) . '", "Cell": [{"column":"' . base64_encode($columnName) . '", "$":"' . base64_encode($columnValue) . '"}]} ';

file_put_contents(__DIR__ . '/moveToHBASE', '{"Row":[' . implode(', ', $apiCallPart) . ']}');
$apiCall .= " " . HBASE_API_URL . "/package/" . time();
exec($apiCall);