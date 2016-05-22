<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';

$id = $_GET['id'];

$result = getFromHBase('/package/' . $id, ['supplier', 'customer', 'address']);

echo json_encode($result[0]);