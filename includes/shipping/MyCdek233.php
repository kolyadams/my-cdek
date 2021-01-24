<?php
if (!defined("ABSPATH")) {
  exit(); // Exit if accessed directly
}
class MyCdek233 extends AbstractMyCdek
{
  protected $tariff_code = 233;

  public function __construct($instance_id = 0)
  {
    $this->id = "mycdek_233";
    $this->instance_id = absint($instance_id);
    $this->method_title = "ТК СДЭК Экономичная посылка склад-дверь";
    $this->method_description =
      "Услуга экономичной наземной доставки товаров по России для компаний, осуществляющих дистанционную торговлю. Услуга действует по направлениям из Москвы в подразделения СДЭК, находящиеся за Уралом и в Крым.";
    $this->title = "ТК СДЭК Экономичная посылка склад-дверь";
    $this->supports = ["shipping-zones", "instance-settings", "instance-settings-modal"];
  }
}
