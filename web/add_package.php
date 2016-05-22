<?php

require_once __DIR__ . '/../rest/config.php';
require_once __DIR__ . '/../rest/google_maps_api.php';
require_once __DIR__ . '/../rest/hbase.php';

addPackage($_POST);
addAdress($_POST);

header("Location: add_package_form.php");