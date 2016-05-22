<?php

function getCourierPreferences($courierId)
{
    $result = getFromHBase('/preferences/' . $courierId);
    if (is_array($result) && count($result) > 0) {
        return $result[0];
    }
    return false;  //  no preferences
}

function getFromHBase($url, $updateCols = array())
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, HBASE_API_URL . $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    $resultAddress = curl_exec($curl);
    $data = json_decode($resultAddress, true);
    $result = array();
    if (isset($data['Row']) && count($data['Row']) > 0) {
        foreach ($data['Row'] as $row_key => $row_val) {
            $result[$row_key] = getRow($row_val['Cell']);
            $result[$row_key]['id'] = getVal($row_val, 'key');
            if (is_array($updateCols) && count($updateCols) > 0) {
                foreach ($result[$row_key] as $col_key => $col_val) {
                    if (preg_match('/^(' . implode('|', $updateCols) . ')_id$/', $col_key, $match)) {
                        addDetailsById($result[$row_key], $match[1], $updateCols);
                    }
                }
            }
        }
    }

    return $result;
}

function addDetailsById(&$row, $key, $updateCols, $removeId = true)
{
    if (isset($row[$key . '_id'])) {
        $details = getFromHBase('/' . $key . '/' . $row[$key . '_id'], $updateCols);
        if (is_array($details) && count($details) > 0) {
            $row[$key] = $details[0];
            if ($removeId) {
                unset($row[$key . '_id']);
            }
        }
    }
}

function getRow($json)
{
    $addressOutput = array();
    foreach ($json as $addressValue) {
        $addressOutput[getColumnName($addressValue)] = getVal($addressValue, '$');
    }
    return $addressOutput;
}

function getColumnName($addressValue)
{
    return preg_replace("/^[a-z]+:/", '', getVal($addressValue, 'column'));
}

function getVal($data, $key)
{
    return base64_decode($data[$key]);
}

function addPackage($packageDetails)
{
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

function addAdress($data)
{
    $apiCall = 'curl -v -X PUT -H "Content-Type: application/json" --data "@moveToHBASE"';

    $address_id = "22";//todo

    $apiCallPart = array();
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:city') . '", "$":"' . base64_encode($data['city']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:street') . '", "$":"' . base64_encode($data['street']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:number') . '", "$":"' . base64_encode($data['number']) . '"}]} ';

    $locationDetails = getLocationDetails($data);
    //$locationDetails = array("lat" => "45.73713", "lng" => "21.2184243");
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:lat') . '", "$":"' . base64_encode($locationDetails['lat']) . '"}]} ';
    $apiCallPart[] = '{"key":"' . base64_encode($address_id) . '", "Cell": [{"column":"' . base64_encode('a:lng') . '", "$":"' . base64_encode($locationDetails['lng']) . '"}]} ';

    file_put_contents(__DIR__ . '/moveToHBASE', '{"Row":[' . implode(', ', $apiCallPart) . ']}');
    $apiCall .= " " . HBASE_API_URL . "/address/" . time();

    return exec($apiCall);

}


function getLocationDetails($data)
{
    $city = $data['city'];
    $street = $data['street'];
    $no = $data['number'];

    $res = curlRequest(getGoogleAPIUrl(
        GOOGLE_API_URL,
        'maps/api/geocode',
        GOOGLE_API_FORMAT,
        GOOGLE_API_KEY,
        [
            'address' => str_replace(" ", "+", $city) . ",+" . str_replace(" ", "+", $street) . ",+" . $no,
        ]
    ));

    return $res["results"][0]["geometry"]["location"];

}