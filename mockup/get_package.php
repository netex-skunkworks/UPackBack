<?php

$result = array("id" => 234,
                "weight" => 23,
                "vol_lenght" => 10,
                "vol_width" => 11,
                "vol_height" => 11,
                "customer" => array(
                    "id" => 456,
                    "address" => array(
                        "id" => 4,
                        "street" => "Liviu Rebreanu",
                        "lat" => 12321.321312,
                        "lng" => 321321.421342,
                        "number" => 3,
                        "city" => "Timisoara")),
                "supplier" => array(
                    "id" => 123,
                    "name" => "Ion Pop",
                    "address" => array(
                        "id" => 4,
                        "street" => "Liviu Rebreanu",
                        "lat" => 12321.321312,
                        "lng" => 321321.421342,
                        "number" => 3,
                        "city" => "Timisoara")));

echo json_encode($result);