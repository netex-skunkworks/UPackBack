<?php

function checkAndFilterIfNotInRange(&$items, $key, $checkValue, $maxValue = NULL) {
    if ( ! checkIfInRange($checkValue, $maxValue) ) {
        removeItem($items, $key);
    }
}

function checkIfInRange($checkValue, $maxValue) {
    if ( empty($maxValue) ) {
        return true;  //  max range not defined
    } elseif ( $checkValue > $maxValue ) {
        return false;  //  $checkValue outside max range
    } else {
        return true;  //  $checkValue inside max range
    }
}

function removeItem(&$items, $key) {
    unset($items[$key]);
}

function getDistance($data) {
    return $data['distance'];
}

function getDuration($data) {
    return $data['duration'];
}

function gapiCalculateDistance($origin, $destination) {
    $result = curlRequest(getGoogleAPIUrl(
        GOOGLE_API_URL,
        GOOGLE_API_FUNC,
        GOOGLE_API_FORMAT,
        GOOGLE_API_KEY,
        [
            'origins' => getGoogleAPILocation($origin),
            'destinations' => getGoogleAPILocation($destination)
        ]
    ));
    if ( checkResult($result) ) {
        return translateResultData($result);
    }
    return false;  //  ERROR
}

function translateResultData($data) {
    $elements = &$data['rows'][0]['elements'][0];
    $result = [
        'addresses' => [
            'origin' => $data['origin_addresses'],
            'destination' => $data['destination_addresses']
        ],
        'description' => [
            'distance' => $elements['distance']['text'],
            'duration' => $elements['duration']['text']
        ],
        'distance' => $elements['distance']['value'],
        'duration' => $elements['duration']['value']
    ];
    return $result;
}

function checkResult($data) {
    if ( !isset($data['status']) ) {
        print_r($data);  //  DEBUG ONLY
        return false ;  //  "status" not found
    } elseif ( $data['status'] != 'OK' ) {
        print_r($data);  //  DEBUG ONLY
        return false ;  //  not OK
    }
    return true;  //  SUCCESS
}

function curlRequest($request) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $request);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $resultAddress = curl_exec($curl);
    $data = json_decode($resultAddress, true);
    if ( empty($data) ) {
        return false;
    }
    return $data;
}

function getGoogleAPILocation($params) {
    return implode(',', $params);
}

function getGoogleAPIUrl($url, $func, $format, $key, $parameters = array()) {
    $result  = '';
    $result .= $url;
    $result .= '/' . $func;
    $result .= '/' . $format;
    $result .= '?' . serializeParameters(array_merge($parameters, ['key' => $key]));
    return $result;
}

function serializeParameters($params) {
    if ( is_array($params) && count($params) > 0 ) {
        $data = array();
        foreach ( $params as $par_key => $par_value ) {
            $data[$par_key] = serializeKeyValue($par_key, $par_value);
        }
        return implode('&', $data);
    }
}

function serializeKeyValue($key, $value) {
    return serializeValue($key) . '=' . serializeValue($value);
}

function serializeValue($value) {
    return $value;
}

?>