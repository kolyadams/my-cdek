<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
class MyCdek137 extends AbstractMyCdek
{
  protected $tariff_code = 137;

  public function __construct($instance_id = 0)
  {
    $this->id = "mycdek_137";
    $this->instance_id = absint($instance_id);
    $this->method_title = "ТК СДЭК Посылка склад-дверь";
    $this->method_description =
      "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";
    $this->title = "ТК СДЭК Посылка склад-дверь";
    $this->supports = ["shipping-zones", "instance-settings", "instance-settings-modal"];
  }
}
