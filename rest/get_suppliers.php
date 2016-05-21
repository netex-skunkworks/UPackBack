<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';
require_once __DIR__ . '/google_maps_api.php';


$result = getFromHBase('/supplier/*', ['address']);

$courierPosition = isset($_GET['position']) ? $_GET['position'] : false ;

if ( isset($courierPosition) ) {
    foreach ($result as $key => $res_value) {
        $supplierId = $res_value['id'];
        $prefs = getSupplierPreferences($supplierId);
        $checkRange = gapiCalculateDistance(
            explode(',', $courierPosition),
            [$res_value['address']['lat'], $res_value['address']['lng']]
        );
        if ( is_array($checkRange) ) {
            checkAndFilterIfNotInRange($result, $key, getDistance($checkRange), $prefs['suppliers_distance']);
            checkAndFilterIfNotInRange($result, $key, getDuration($checkRange), $prefs['suppliers_duration']);
        }
    }
}

echo json_encode($result);

function getSupplierPreferences($supplierId) {
    $result = getFromHBase('/preferences/'.$supplierId);
    if ( is_array($result) && count($result) > 0 ) {
        return $result[0];
    }
    return false;  //  no preferences
}