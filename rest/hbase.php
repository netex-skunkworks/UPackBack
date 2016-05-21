<?php

function getFromHBase($url, $updateCols = array()) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, HBASE_API_URL.$url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    $resultAddress = curl_exec($curl);
    $data = json_decode($resultAddress, true);
    $result = array();
    if ( isset($data['Row']) && count($data['Row']) > 0 ) {
        foreach ($data['Row'] as $row_key => $row_val) {
            $result[$row_key] = getRow($row_val['Cell']);
            $result[$row_key]['id'] = getVal($row_val, 'key');
            if ( is_array($updateCols) && count($updateCols) > 0 ) {
                foreach ( $result[$row_key] as $col_key => $col_val ) {
                    if ( preg_match('/^('.implode('|', $updateCols).')_id$/', $col_key, $match) ) {
                        addDetailsById($result[$row_key], $match[1], $updateCols);
                    }
                }
            }
        }
    }
    return $result;
}

function addDetailsById(&$row, $key, $updateCols, $removeId = true) {
    if ( isset($row[$key.'_id']) ) {
        $details = getFromHBase('/'.$key.'/' . $row[$key.'_id'], $updateCols);
        if ( is_array($details) && count($details) > 0 ) {
            $row[$key] = $details[0];
            if ( $removeId ) {
                unset($row[$key.'_id']);
            }
        }
    }
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