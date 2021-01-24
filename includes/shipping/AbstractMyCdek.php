<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
abstract class AbstractMyCdek extends WC_Shipping_Method
{
  public function calculate_shipping($package = [])
  {
    $cdek_api = CdekApi::instance();
    $mygeo = MyGeo::instance();
    if(empty($mygeo->state["cdek_city_code"])){
      $cost = 0;
    }
    else{
      $cdek_api->get_tariff($this->tariff_code);
      $cost = isset($cdek_api->tariff["total_sum"]) ? ceil(($cdek_api->tariff["total_sum"] + ($cdek_api->tariff["total_sum"] * 0.2))/10)*10 : 0;
    }
    

    $rate = [
      "id" => $this->id,
      "label" => $this->title,
      "cost" => $cost,
    ];

    $this->add_rate($rate);
  }
}
