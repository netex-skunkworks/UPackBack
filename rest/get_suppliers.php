<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';
require_once __DIR__ . '/google_maps_api.php';


$result = getFromHBase('/supplier/*', ['address']);

$courierPosition = isset($_GET['position']) ? $_GET['position'] : false ;
$maxDistance = isset($_GET['distance']) ? $_GET['distance'] : false ;
$maxDuration = isset($_GET['duration']) ? $_GET['duration'] : false ;

foreach ($result as $key => $res_value) {
    if ( isset($result[$key]) && isset($courierPosition) && ( !empty($maxDistance) || !empty($maxDuration) ) ) {
        $checkRange = gapiCalculateDistance(
            explode(',', $courierPosition),
            [$res_value['address']['lat'], $res_value['address']['lng']]
        );
        if ( is_array($checkRange) ) {
            checkAndFilterIfNotInRange($result, $key, getDistance($checkRange), $maxDistance);
            checkAndFilterIfNotInRange($result, $key, getDuration($checkRange), $maxDuration);
        }
    }
}


echo json_encode($result);
