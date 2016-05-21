<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';

$supplierId = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : null;
$courierId = isset($_GET['courier_id']) ? $_GET['courier_id'] : null;
$packageId = isset($_GET['package_id']) ? $_GET['package_id'] : null;
$status = isset($_GET['status']) ? strtolower($_GET['status']) : null;

$result = getFromHBase('/package/*', ['supplier', 'customer', 'address', 'courier']);
$size = count($result);

foreach ($result as $res_key => $res_value) {
    if (strtolower($result[$res_key]['status']) != $status) {
        unset($result[$res_key]);
    } else if ($supplierId) {
        if ($result[$res_key]['supplier']['id'] != $supplierId) {
            unset($result[$res_key]);
        }
    } else if ($courierId) {
        if (array_key_exists('courier', $result[$res_key]) && $result[$res_key]['courier']['id'] != $courierId) {
            unset($result[$res_key]);
        } else if (array_key_exists('courier_id', $result[$res_key])) {
            unset($result[$res_key]);
        }
    }
}


echo json_encode($result, JSON_PRETTY_PRINT);