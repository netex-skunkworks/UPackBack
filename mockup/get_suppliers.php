<?php

$result = array(array("id" => 123,
                      "name" => "Ion Pop",
                      "address" => array("id" => 4,
                                         "street" => "Liviu Rebreanu",
                                         "lat" => 12321.321312,
                                         "lng" => 321321.421342,
                                         "number" => 3,
                                         "city" => "Timisoara")));

echo json_encode($result);