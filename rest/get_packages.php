<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';
require_once __DIR__ . '/google_maps_api.php';

$courierId = isset($_GET['courier_id']) ? $_GET['courier_id'] : null;
$supplierId = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : null;
$status = isset($_GET['status']) ? strtolower($_GET['status']) : null;

//  mandatory parameters
if ( empty($courierId) || empty($status) ) {
    echo json_encode(array(), JSON_PRETTY_PRINT);
    die();
}

$courierPosition = isset($_GET['position']) ? $_GET['position'] : false ;

if ( ! empty($courierPosition) ) {
    $prefs = getCourierPreferences($courierId);
    $origin = explode(',', $courierPosition);
} else {
    $prefs = false;
}

$result = getFromHBase('/package/*', ['supplier', 'customer', 'address', 'courier']);

foreach ($result as $res_key => $res_value) {

    $via = [
        $res_value['supplier']['address']['lat'],
        $res_value['supplier']['address']['lng']
    ];
    $destination = [
        $res_value['customer']['address']['lat'],
        $res_value['customer']['address']['lng']
    ];

    if ( strtolower($res_value['status']) != $status ) {
        removeItem($result, $res_key);  //  filter non-matched packages by status
    } elseif ( $status == 'available' ) {

        if ( ! isset($res_value['supplier']) ) {
            removeItem($result, $res_key);  //  broken packages (no supplier details)
        } elseif ( ! isset($res_value['supplier']['id']) ) {
            removeItem($result, $res_key);  //  broken packages (no supplier details)
        } elseif ( $res_value['supplier']['id'] != $supplierId ) {
            removeItem($result, $res_key);  //  is from a different supplier
        } else
        if ( ! empty($prefs) ) {

            //  filter by courier - supplier - package range

            $maxDistance = $prefs['delivery_distance'];
            $maxDuration = $prefs['delivery_duration'];

            $checkSupplier = gapiCalculateDistance($origin, $via);
            $checkDelivery = gapiCalculateDistance($via, $destination);
            $checkRange = combineResultData($checkSupplier, $checkDelivery);

            if ( is_array($checkRange) ) {
                checkAndFilterIfNotInRange($result, $res_key, getDistance($checkRange), $maxDistance);
                checkAndFilterIfNotInRange($result, $res_key, getDuration($checkRange), $maxDuration);
            }

            if ( isset($result[$res_key]) ) {
                $result[$res_key]['delivery_description'] = $checkDelivery['description'];  //  add delivery estimation description
            }
        }
    } elseif ( !isset($res_value['courier']) ) {
        removeItem($result, $res_key);  //  filter package with non associated courier
    } elseif ( !isset($res_value['courier']['id']) ) {
        removeItem($result, $res_key);  //  filter package with non associated courier
    } elseif ( $res_value['courier']['id'] != $courierId ) {
        removeItem($result, $res_key);  //  filter package associated to another courier
    } elseif ( $status == 'accepted' ) {
        if ( $checkDelivery = gapiCalculateDistance($via, $destination) ) {
            $result[$res_key]['delivery_description'] = $checkDelivery['description'];  //  add delivery estimation description
        }
    } elseif ( $status == 'enroute' ) {
        if ( ! empty($origin) && ( $checkDelivery = gapiCalculateDistance($origin, $destination) ) ) {
            $result[$res_key]['delivery_description'] = $checkDelivery['description'];  //  add delivery estimation description
        }
    }
}

echo json_encode(array_values($result));