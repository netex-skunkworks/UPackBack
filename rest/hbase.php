<?php

function getFromHBase($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, HBASE_API_URL.$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    $resultAddress = curl_exec($curl);
    $data = json_decode($resultAddress, true);
    foreach ($data['Row'] as $row_key => $row_val) {
        $result[$row_key] = getRow($row_val['Cell']);
        $result[$row_key]['id'] = getVal($row_val, 'key');
    }
    return $result;
}

function getRow($json) {
    $addressOutput = array();
    foreach ($json as $addressValue) {
        $addressOutput[getColumnName($addressValue)] = getVal($addressValue, '$');
    }
    return $addressOutput;
}

function getColumnName($addressValue) {
    return preg_replace("/^[a-z]+:/", '', getVal($addressValue, 'column'));
}

function getVal($data, $key) {
    return base64_decode($data[$key]);
}

?>