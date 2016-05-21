<?php

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/hbase.php';


$result = getFromHBase('/package/*', ['supplier', 'customer', 'address']);

echo json_encode($result);

?>