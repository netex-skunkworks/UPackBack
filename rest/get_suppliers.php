<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';


$result = getFromHBase('/supplier/*');
foreach ($result as $key => $supplier) {
    if ( isset($supplier['address_id']) ) {
        $addr = getFromHBase('/address/' . $supplier['address_id']);
        $result[$key]['address'] = $addr[0];
        unset($result[$key]['address_id']);
    }
}

echo json_encode($result);
