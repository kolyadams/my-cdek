<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
class MyCdek234 extends AbstractMyCdek
{
  protected $tariff_code = 234;

  public function __construct($instance_id = 0)
  {
    $this->id = "mycdek_pvz_234";
    $this->instance_id = absint($instance_id);
    $this->method_title = "ТК СДЭК Экономичная посылка склад-склад";
    $this->method_description =
      "Услуга экономичной доставки товаров по России для компаний, осуществляющих дистанционную торговлю.";
    $this->title = "ТК СДЭК Экономичная посылка склад-склад";
    $this->supports = ["shipping-zones", "instance-settings", "instance-settings-modal"];
  }
}
