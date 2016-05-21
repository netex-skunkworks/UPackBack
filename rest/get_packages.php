<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';
require_once __DIR__ . '/google_maps_api.php';

$courierId = isset($_GET['courier_id']) ? $_GET['courier_id'] : null;
$status = isset($_GET['status']) ? strtolower($_GET['status']) : null;

//  mandatory parameters
if ( empty($courierId) || empty($status) ) {
    echo json_encode(array(), JSON_PRETTY_PRINT);
    die();
}

$courierPosition = isset($_GET['position']) ? $_GET['position'] : false ;

if ( ! empty($courierPosition) ) {
    $prefs = getCourierPreferences($courierId);
} else {
    $prefs = false;
}

$result = getFromHBase('/package/*', ['supplier', 'customer', 'address', 'courier']);

foreach ($result as $res_key => $res_value) {

    if ( strtolower($res_value['status']) != $status ) {
        removeItem($result, $res_key);  //  filter non-matched packages by status
    } elseif ( $status == 'available' ) {

        //  filter by courier - supplier - package range
        if ( ! empty($prefs) ) {
            $origin = explode(',', $courierPosition);
            $via = [
                $res_value['supplier']['address']['lat'],
                $res_value['supplier']['address']['lng']
            ];
            $destination = [
                $res_value['customer']['address']['lat'],
                $res_value['customer']['address']['lng']
            ];
            $maxDistance = $prefs['delivery_distance'];
            $maxDuration = $prefs['delivery_duration'];
            $checkRange = gapiCalculateViaDistance($origin, $via, $destination);
            if ( is_array($checkRange) ) {
                checkAndFilterIfNotInRange($result, $res_key, getDistance($checkRange), $maxDistance);
                checkAndFilterIfNotInRange($result, $res_key, getDuration($checkRange), $maxDuration);
            }
        }
    } elseif ( !isset($res_value['courier']) ) {
        removeItem($result, $res_key);  //  filter package with non associated courier
    } elseif ( !isset($res_value['courier']['id']) ) {
        removeItem($result, $res_key);  //  filter package with non associated courier
    } elseif ( $res_value['courier']['id'] != $courierId ) {
        removeItem($result, $res_key);  //  filter package associated to another courier
    }
}

echo json_encode(array_values($result));