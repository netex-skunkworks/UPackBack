<?php

$result = array(array("id" => 234,
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
                              "city" => "Timisoara"))),
                array("id" => 2334,
                    "weight" => 223,
                    "vol_lenght" => 120,
                    "vol_width" => 121,
                    "vol_height" => 131,
                    "customer" => array(
                        "id" => 4536,
                        "address" => array(
                            "id" => 5,
                            "street" => "Liviu Rebreanu",
                            "lat" => 12221.321312,
                            "lng" => 3233321.421342,
                            "number" => 5,
                            "city" => "Timisoara")),
                    "supplier" => array(
                        "id" => 12443,
                        "name" => "Ion Pop",
                        "address" => array(
                            "id" => 8,
                            "street" => "Liviu Rebreanu",
                            "lat" => 121.321312,
                            "lng" => 321.421342,
                            "number" => 89,
                            "city" => "Timisoara"))));

echo json_encode($result);