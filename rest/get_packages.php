<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';

$supplierId = $_GET['supplier_id'];
$result = getFromHBase('/package/*', ['supplier', 'customer', 'address']);

$size  = count($result);
for ($i = 0; $i < $size; $i++) {
    if ($result[$i]['supplier']['id'] != $supplierId) {
        unset($result[$i]);
    }
}

echo json_encode($result);