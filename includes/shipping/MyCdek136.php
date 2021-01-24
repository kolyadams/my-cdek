<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
class MyCdek136 extends AbstractMyCdek
{
  protected $tariff_code = 136;

  public function __construct($instance_id = 0)
  {
    $this->id = "mycdek_pvz_136";
    $this->instance_id = absint($instance_id);
    $this->method_title = "ТК СДЭК Посылка склад-склад";
    $this->method_description =
      "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";
    $this->title = "ТК СДЭК Посылка склад-склад";
    $this->supports = ["shipping-zones", "instance-settings", "instance-settings-modal"];
  }
}
